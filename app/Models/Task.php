<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}