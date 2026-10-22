<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TaskAssignee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // synchronize task for assignees, including soft-deleted
    public static function syncForTask(int $taskId, array $userIds): void
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        $now     = now();

        // All existing rows (including soft-deleted) for this task, keyed by user_id.
        $existingRows = DB::table('task_assignees')
            ->where('task_id', $taskId)
            ->get()
            ->keyBy('user_id');

        // Insert or reactivate
        foreach ($userIds as $userId) {
            if ($existingRows->has($userId)) {
                DB::table('task_assignees')
                    ->where('id', $existingRows[$userId]->id)
                    ->update([
                        'is_active'  => 1,
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('task_assignees')->insert([
                    'task_id'    => $taskId,
                    'user_id'    => $userId,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Soft-delete rows that are no longer selected
        foreach ($existingRows as $userId => $row) {
            if (! in_array($userId, $userIds, true) && $row->deleted_at === null) {
                DB::table('task_assignees')
                    ->where('id', $row->id)
                    ->update([
                        'is_active'  => 0,
                        'deleted_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public static function isActiveAssignee(int $taskId, int $userId): bool
    {
        return DB::table('task_assignees')
            ->where('task_id', $taskId)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->exists();
    }
}
