<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'category_id',
        'title',
        'description',
        'status',
        'due_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            TaskCategory::class,
            'category_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'task_assignees',
            'task_id',
            'user_id'
        )
            ->withPivot(['id', 'is_active', 'deleted_at'])
            ->withTimestamps();
    }

    // ---------------------------------------------------------------
    // Dropdown sources
    // ---------------------------------------------------------------

    public static function getActiveCategories()
    {
        return DB::table('task_categories')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    // list for users assignment
    public static function getUsersForAssignment()
    {
        return DB::table('users')
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }


    // Get tasks for the kanban board view
    public static function getBoardTasks(array $filters, int $currentUserId): array
    {
        $filter = $filters['filter'] ?? 'all';

        $query = DB::table('tasks')
            ->leftJoin('task_categories', 'task_categories.id', '=', 'tasks.category_id')
            ->leftJoin('users as creators', 'creators.id', '=', 'tasks.created_by')
            ->select([
                'tasks.id',
                'tasks.title',
                'tasks.description',
                'tasks.status',
                'tasks.due_date',
                'tasks.created_by',
                'tasks.category_id',
                'tasks.is_active',
                'task_categories.name as category_name',
                'creators.first_name as creator_first_name',
                'creators.last_name as creator_last_name',
            ])
            ->whereNull('tasks.deleted_at')
            ->where('tasks.is_active', 1);

        // Filter
        if ($filter === 'assigned_by_me') {
            $query->where('tasks.created_by', $currentUserId);
        } elseif ($filter === 'assigned_to_me') {
            $query->whereIn('tasks.id', function ($sub) use ($currentUserId) {
                $sub->select('task_id')
                    ->from('task_assignees')
                    ->where('user_id', $currentUserId)
                    ->where('is_active', 1)
                    ->whereNull('deleted_at');
            });
        } else {
            // All task 
            $query->where(function ($q) use ($currentUserId) {
                $q->where('tasks.created_by', $currentUserId)
                  ->orWhereIn('tasks.id', function ($sub) use ($currentUserId) {
                      $sub->select('task_id')
                          ->from('task_assignees')
                          ->where('user_id', $currentUserId)
                          ->where('is_active', 1)
                          ->whereNull('deleted_at');
                  });
            });
        }

        // Optionals
        if (! empty($filters['search'])) {
            $query->where('tasks.title', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('tasks.category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('tasks.status', $filters['status']);
        }

        if (! empty($filters['overdue'])) {
            $query->whereNotNull('tasks.due_date')
                ->whereDate('tasks.due_date', '<', now()->toDateString())
                ->where('tasks.status', '!=', 'completed');
        } elseif (! empty($filters['due_date'])) {
            $query->whereDate('tasks.due_date', $filters['due_date']);
        }

        $rows = $query->orderBy('tasks.due_date')->orderByDesc('tasks.id')->get();

        // fetch of assignees for all tasks
        $taskIds     = $rows->pluck('id')->all();
        $assigneeMap = [];

        if (! empty($taskIds)) {
            $assigneeRows = DB::table('task_assignees')
                ->join('users', 'users.id', '=', 'task_assignees.user_id')
                ->whereIn('task_assignees.task_id', $taskIds)
                ->where('task_assignees.is_active', 1)
                ->whereNull('task_assignees.deleted_at')
                ->select(
                    'task_assignees.task_id',
                    'users.id',
                    'users.first_name',
                    'users.last_name'
                )
                ->get();

            foreach ($assigneeRows as $row) {
                $assigneeMap[$row->task_id][] = [
                    'id'   => $row->id,
                    'name' => trim($row->first_name . ' ' . $row->last_name),
                ];
            }
        }

        // list 
        $user = auth()->user();

        $tasks = [];
        foreach ($rows as $row) {
            $actions = '';

            if ($user->can('edit tasks')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-success btn-sm edit-task">
                        Edit
                    </button> ';
            }

            if ($user->can('delete tasks')) {
                $actions .= '
                    <button type="button"
                        data-id="' . $row->id . '"
                        class="btn btn-danger btn-sm delete-task">
                        Delete
                    </button>';
            }

            $tasks[] = [
                'id'          => $row->id,
                'title'       => $row->title,
                'description' => $row->description,
                'status'      => $row->status,
                'due_date'    => $row->due_date,
                'category_id' => $row->category_id,
                'category'    => $row->category_name,
                'created_by'  => $row->created_by,
                'creator'     => trim(($row->creator_first_name ?? '') . ' ' . ($row->creator_last_name ?? '')),
                'assignees'   => $assigneeMap[$row->id] ?? [],
                'action'      => $actions ?: '—',
            ];
        }

        return $tasks;
    }

    // ---------------------------------------------------------------
    // Single-task reads
    // ---------------------------------------------------------------

    // find single task for edit modal
    public static function getTaskForEdit($id)
    {
        $task = DB::table('tasks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $task) {
            return null;
        }

        $assigneeIds = DB::table('task_assignees')
            ->where('task_id', $id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->pluck('user_id')
            ->all();

        return [
            'id'           => $task->id,
            'title'        => $task->title,
            'description'  => $task->description,
            'category_id'  => $task->category_id,
            'status'       => $task->status,
            'due_date'     => $task->due_date,
            'is_active'    => (bool) $task->is_active,
            'assignee_ids' => array_map('intval', $assigneeIds),
        ];
    }

    public static function findById($id)
    {
        return DB::table('tasks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function existsById($id)
    {
        return DB::table('tasks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }

    // return if the task is accessible by user
    public static function isAccessibleBy($task, int $userId): bool
    {
        if ((int) $task->created_by === $userId) {
            return true;
        }

        return DB::table('task_assignees')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->exists();
    }

    // ---------------------------------------------------------------
    // Dashboard
    // ---------------------------------------------------------------

    public static function getStatusCounts(?int $userId = null)
    {
        $query = DB::table('tasks')
            ->select('status', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('status');

        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        return $query->get();
    }

    public static function getMonthlyCounts(?int $userId = null)
    {
        $query = DB::table('tasks')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month');

        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        return $query->get();
    }

    public static function getCategoryCounts(?int $userId = null)
    {
        $query = DB::table('tasks')
            ->join('task_categories', 'task_categories.id', '=', 'tasks.category_id')
            ->select('task_categories.name', DB::raw('count(*) as total'))
            ->whereNull('tasks.deleted_at')
            ->whereNull('task_categories.deleted_at')
            ->groupBy('task_categories.name');

        if ($userId !== null) {
            $query->where('tasks.created_by', $userId);
        }

        return $query->get();
    }

    public static function getOverdueCount(?int $userId = null): int
    {
        $query = DB::table('tasks')
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now()->toDateString())
            ->whereNull('deleted_at');

        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        return $query->count();
    }

    public static function getOnTimeCount(?int $userId = null): int
    {
        $query = DB::table('tasks')
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('due_date', '>=', now()->toDateString())
                    ->orWhereNull('due_date');
            })
            ->whereNull('deleted_at');

        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        return $query->count();
    }

    public static function getTotals(?int $userId = null): array
    {
        $base = function () use ($userId) {
            $query = DB::table('tasks')->whereNull('deleted_at');

            if ($userId !== null) {
                $query->where('created_by', $userId);
            }

            return $query;
        };

        return [
            'tasks' => $base()->count(),
            'pending' => $base()->where('status', 'pending')->count(),
            'in_progress' => $base()->where('status', 'in_progress')->count(),
            'completed' => $base()->where('status', 'completed')->count(),
        ];
    }

    public static function getAssigneeCounts()
    {
        return DB::table('task_assignees')
            ->join('users', 'users.id', '=', 'task_assignees.user_id')
            ->join('tasks', 'tasks.id', '=', 'task_assignees.task_id')
            ->select('users.first_name', 'users.last_name', DB::raw('count(*) as total'))
            ->whereNull('task_assignees.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNull('tasks.deleted_at')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public static function getOverdueTrend()
    {
        return DB::table('tasks')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now()->toDateString())
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    // ---------------------------------------------------------------
    // Dashboard - Personal (my overview)
    // ---------------------------------------------------------------

    protected static function personalBase(int $userId)
    {
        return DB::table('tasks')
            ->whereNull('tasks.deleted_at')
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereIn('tasks.id', function ($sub) use ($userId) {
                        $sub->select('task_id')
                            ->from('task_assignees')
                            ->where('user_id', $userId)
                            ->where('is_active', 1)
                            ->whereNull('task_assignees.deleted_at');
                    });
            });
    }

    public static function getPersonalStatusCounts(int $userId)
    {
        return self::personalBase($userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
    }

    public static function getPersonalMonthlyCounts(int $userId)
    {
        return self::personalBase($userId)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public static function getPersonalCategoryCounts(int $userId)
    {
        return self::personalBase($userId)
            ->join('task_categories', 'task_categories.id', '=', 'tasks.category_id')
            ->select('task_categories.name', DB::raw('count(*) as total'))
            ->whereNull('task_categories.deleted_at')
            ->groupBy('task_categories.name')
            ->get();
    }

    public static function getPersonalOverdueCount(int $userId): int
    {
        return self::personalBase($userId)
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now()->toDateString())
            ->count();
    }

    public static function getPersonalOnTimeCount(int $userId): int
    {
        return self::personalBase($userId)
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('due_date', '>=', now()->toDateString())
                    ->orWhereNull('due_date');
            })
            ->count();
    }

    public static function getPersonalTotals(int $userId): array
    {
        return [
            'tasks' => self::personalBase($userId)->count(),
            'pending' => self::personalBase($userId)->where('status', 'pending')->count(),
            'in_progress' => self::personalBase($userId)->where('status', 'in_progress')->count(),
            'completed' => self::personalBase($userId)->where('status', 'completed')->count(),
        ];
    }

    // ---------------------------------------------------------------
    // Writes
    // ---------------------------------------------------------------

    /**
     * Create a task + assignees in one transaction.
     * created_by is always derived server-side.
     */
    public static function createTask(array $data, int $creatorId)
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $taskId = DB::table('tasks')->insertGetId([
                'created_by'  => $creatorId,
                'category_id' => $data['category_id'],
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'status'      => $data['status'],
                'due_date'    => $data['due_date'] ?? null,
                'is_active'   => ! empty($data['is_active']) ? 1 : 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            \App\Models\TaskAssignee::syncForTask($taskId, $data['assignee_ids'] ?? []);

            return $taskId;
        });
    }

    // update task assignees and other fields in one transaction
    public static function updateTask($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            DB::table('tasks')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'category_id' => $data['category_id'],
                    'title'       => $data['title'],
                    'description' => $data['description'] ?? null,
                    'status'      => $data['status'],
                    'due_date'    => $data['due_date'] ?? null,
                    'is_active'   => ! empty($data['is_active']) ? 1 : 0,
                    'updated_at'  => now(),
                ]);

            if (array_key_exists('assignee_ids', $data)) {
                TaskAssignee::syncForTask((int) $id, $data['assignee_ids'] ?? []);
            }
        });
    }

    // drag and drop status update
    public static function updateStatus($id, string $status)
    {
        return DB::table('tasks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);
    }

    // Soft delete task
    public static function deleteTask($id)
    {
        return DB::table('tasks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }
}