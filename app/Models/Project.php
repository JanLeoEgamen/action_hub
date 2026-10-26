<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'status',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // ---------------------------------------------------------------
    // Dropdown / lookup sources
    // ---------------------------------------------------------------

    public static function getUsersForSelection()
    {
        return DB::table('users')
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    // ---------------------------------------------------------------
    // Project list
    // ---------------------------------------------------------------

    public static function getProjectList(array $filters, int $currentUserId): array
    {
        $filter = $filters['filter'] ?? 'all';

        $query = DB::table('projects')
            ->leftJoin('users as owners', 'owners.id', '=', 'projects.owner_id')
            ->select([
                'projects.id',
                'projects.name',
                'projects.description',
                'projects.status',
                'projects.is_active',
                'projects.start_date',
                'projects.end_date',
                'projects.owner_id',
                'projects.created_at',
                'owners.first_name as owner_first_name',
                'owners.last_name  as owner_last_name',
            ])
            ->whereNull('projects.deleted_at');

        // Filters
        if ($filter === 'owned_by_me') {
            $query->where('projects.owner_id', $currentUserId);
        } elseif ($filter === 'member_of') {
            $query->whereIn('projects.id', function ($sub) use ($currentUserId) {
                $sub->select('project_id')
                    ->from('project_members')
                    ->where('user_id', $currentUserId)
                    ->where('is_active', 1)
                    ->whereNull('deleted_at');
            });
        } else {
            $query->where(function ($q) use ($currentUserId) {
                $q->where('projects.owner_id', $currentUserId)
                  ->orWhereIn('projects.id', function ($sub) use ($currentUserId) {
                      $sub->select('project_id')
                          ->from('project_members')
                          ->where('user_id', $currentUserId)
                          ->where('is_active', 1)
                          ->whereNull('deleted_at');
                  });
            });
        }

        if (! empty($filters['search'])) {
            $query->where('projects.name', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['status'])) {
            $query->where('projects.status', $filters['status']);
        }

        if (! empty($filters['is_active']) && $filters['is_active'] !== 'all') {
            $query->where('projects.is_active', (int) $filters['is_active']);
        }

        $rows = $query->orderByDesc('projects.id')->get();

        $projectIds = $rows->pluck('id')->all();

        // Ticket counts per project (used for summary cards)
        $ticketCounts = [];
        if (! empty($projectIds)) {
            $counts = DB::table('tickets')
                ->whereIn('project_id', $projectIds)
                ->whereNull('deleted_at')
                ->where('is_active', 1)
                ->select('project_id', DB::raw('COUNT(*) as total'))
                ->groupBy('project_id')
                ->get();

            foreach ($counts as $c) {
                $ticketCounts[$c->project_id] = (int) $c->total;
            }
        }

        $user = auth()->user();
        $projects = [];

        foreach ($rows as $row) {
            $actions = '';

            if ($user->can('edit projects')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-success btn-sm edit-project">
                        Edit
                    </button> ';
            }

            if ($user->can('delete projects')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-danger btn-sm delete-project">
                        Delete
                    </button> ';
            }

            $actions .= '
                <a href="' . route('projects.tickets', $row->id) . '"
                    class="btn btn-primary btn-sm">
                    Open Board
                </a>';

            $projects[] = [
                'id'          => $row->id,
                'name'        => $row->name,
                'description' => $row->description,
                'status'      => $row->status,
                'is_active'   => (bool) $row->is_active,
                'start_date'  => $row->start_date,
                'end_date'    => $row->end_date,
                'owner'       => trim(($row->owner_first_name ?? '') . ' ' . ($row->owner_last_name ?? '')),
                'tickets'     => $ticketCounts[$row->id] ?? 0,
                'action'      => $actions ?: '—',
            ];
        }

        return $projects;
    }

    // ---------------------------------------------------------------
    // Single-project reads
    // ---------------------------------------------------------------

    public static function getProjectForEdit($id)
    {
        $project = DB::table('projects')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $project) {
            return null;
        }

        $memberIds = DB::table('project_members')
            ->where('project_id', $id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->pluck('user_id')
            ->all();

        return [
            'id'          => $project->id,
            'name'        => $project->name,
            'description' => $project->description,
            'status'      => $project->status,
            'owner_id'    => $project->owner_id,
            'start_date'  => $project->start_date,
            'end_date'    => $project->end_date,
            'is_active'   => (bool) $project->is_active,
            'member_ids'  => array_map('intval', $memberIds),
        ];
    }

    public static function findById($id)
    {
        return DB::table('projects')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function existsById($id): bool
    {
        return DB::table('projects')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }

    // ---------------------------------------------------------------
    // Writes
    // ---------------------------------------------------------------

    public static function createProject(array $data, int $ownerId)
    {
        return DB::transaction(function () use ($data, $ownerId) {
            $projectId = DB::table('projects')->insertGetId([
                'owner_id'    => $ownerId,
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'status'      => $data['status'],
                'is_active'   => ! empty($data['is_active']) ? 1 : 0,
                'start_date'  => $data['start_date'] ?? null,
                'end_date'    => $data['end_date'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            ProjectMember::syncForProject($projectId, $data['member_ids'] ?? []);

            return $projectId;
        });
    }

    public static function updateProject($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            DB::table('projects')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'name'        => $data['name'],
                    'description' => $data['description'] ?? null,
                    'status'      => $data['status'],
                    'is_active'   => ! empty($data['is_active']) ? 1 : 0,
                    'start_date'  => $data['start_date'] ?? null,
                    'end_date'    => $data['end_date'] ?? null,
                    'updated_at'  => now(),
                ]);

            if (array_key_exists('member_ids', $data)) {
                ProjectMember::syncForProject((int) $id, $data['member_ids'] ?? []);
            }
        });
    }

    public static function deleteProject($id)
    {
        return DB::table('projects')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }
}