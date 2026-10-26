<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'label',
        'due_date',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'due_date'  => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // kanban board tickets
    public static function getBoardTickets(int $projectId, array $filters): array
    {
        $query = DB::table('tickets')
            ->leftJoin('users as creators', 'creators.id', '=', 'tickets.project_id') // placeholder join
            ->select([
                'tickets.id',
                'tickets.project_id',
                'tickets.title',
                'tickets.description',
                'tickets.status',
                'tickets.label',
                'tickets.priority',
                'tickets.due_date',
                'tickets.is_active',
            ])
            ->whereNull('tickets.deleted_at')
            ->where('tickets.is_active', 1)
            ->where('tickets.project_id', $projectId);

        if (!empty($filters['search'])) {
            $query->where('tickets.title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['label'])) {
            $query->where('tickets.label', $filters['label']);
        }

        if (!empty($filters['priority'])) {
            $query->where('tickets.priority', $filters['priority']);
        }

        if (!empty($filters['status'])) {
            $query->where('tickets.status', $filters['status']);
        }

        if (!empty($filters['overdue'])) {
            $query->whereNotNull('tickets.due_date')
                ->whereDate('tickets.due_date', '<', now()->toDateString())
                ->where('tickets.status', '!=', 'closed');
        } elseif (! empty($filters['due_date'])) {
            $query->whereDate('tickets.due_date', $filters['due_date']);
        }

        $rows = $query->orderBy('tickets.due_date')->orderByDesc('tickets.id')->get();

        $user = auth()->user();
        $tickets = [];

        foreach ($rows as $row) {
            $actions = '';

            if ($user->can('edit tickets')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-success btn-sm edit-ticket">
                        Edit
                    </button> ';
            }

            if ($user->can('delete tickets')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-danger btn-sm delete-ticket">
                        Delete
                    </button>';
            }

            $tickets[] = [
                'id'          => $row->id,
                'title'       => $row->title,
                'description' => $row->description,
                'status'      => $row->status,
                'label'       => $row->label,
                'priority'    => $row->priority,
                'due_date'    => $row->due_date,
                'action'      => $actions ?: '—',
            ];
        }

        return $tickets;
    }

    // single ticket retreive
    public static function getTicketForEdit($id)
    {
        $ticket = DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $ticket) {
            return null;
        }

        return [
            'id'          => $ticket->id,
            'project_id'  => $ticket->project_id,
            'title'       => $ticket->title,
            'description' => $ticket->description,
            'status'      => $ticket->status,
            'label'       => $ticket->label,
            'priority'    => $ticket->priority,
            'due_date'    => $ticket->due_date,
            'is_active'   => (bool) $ticket->is_active,
        ];
    }

    public static function findById($id)
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function existsById($id): bool
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }

    // write
    public static function createTicket(array $data, int $projectId)
    {
        return DB::table('tickets')->insertGetId([
            'project_id'  => $projectId,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'label'       => $data['label'],
            'priority'    => $data['priority'],
            'due_date'    => $data['due_date'] ?? null,
            'is_active'   => ! empty($data['is_active']) ? 1 : 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public static function updateTicket($id, array $data)
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'status'      => $data['status'],
                'label'       => $data['label'],
                'priority'    => $data['priority'],
                'due_date'    => $data['due_date'] ?? null,
                'is_active'   => ! empty($data['is_active']) ? 1 : 0,
                'updated_at'  => now(),
            ]);
    }

    public static function updateStatus($id, string $status)
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);
    }

    public static function deleteTicket($id)
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }
}