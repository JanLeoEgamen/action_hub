<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProjectMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Sync project members, including soft-deleted rows
    public static function syncForProject(int $projectId, array $userIds): void
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        $now     = now();

        $existingRows = DB::table('project_members')
            ->where('project_id', $projectId)
            ->get()
            ->keyBy('user_id');

        foreach ($userIds as $userId) {
            if ($existingRows->has($userId)) {
                DB::table('project_members')
                    ->where('id', $existingRows[$userId]->id)
                    ->update([
                        'is_active'  => 1,
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('project_members')->insert([
                    'project_id' => $projectId,
                    'user_id'    => $userId,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach ($existingRows as $userId => $row) {
            if (! in_array($userId, $userIds, true) && $row->deleted_at === null) {
                DB::table('project_members')
                    ->where('id', $row->id)
                    ->update([
                        'is_active'  => 0,
                        'deleted_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public static function isActiveMember(int $projectId, int $userId): bool
    {
        return DB::table('project_members')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->exists();
    }
}