<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TicketAssignee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // synchronize ticket assignees, including soft-deleted
    public static function syncForTicket(int $ticketId, array $userIds): void
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        $now     = now();

        // All existing rows (including soft-deleted) for this ticket, keyed by user_id.
        $existingRows = DB::table('ticket_assignees')
            ->where('ticket_id', $ticketId)
            ->get()
            ->keyBy('user_id');

        // Insert or reactivate
        foreach ($userIds as $userId) {
            if ($existingRows->has($userId)) {
                DB::table('ticket_assignees')
                    ->where('id', $existingRows[$userId]->id)
                    ->update([
                        'is_active'  => 1,
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('ticket_assignees')->insert([
                    'ticket_id'    => $ticketId,
                    'user_id'      => $userId,
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }

        // Soft-delete rows that are no longer selected
        foreach ($existingRows as $userId => $row) {
            if (! in_array($userId, $userIds, true) && $row->deleted_at === null) {
                DB::table('ticket_assignees')
                    ->where('id', $row->id)
                    ->update([
                        'is_active'  => 0,
                        'deleted_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public static function isActiveAssignee(int $ticketId, int $userId): bool
    {
        return DB::table('ticket_assignees')
            ->where('ticket_id', $ticketId)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->exists();
    }
}