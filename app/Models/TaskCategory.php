<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class TaskCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'category_id');
    }

    // Get all categories that have not been soft-deleted
    public static function getAll()
    {
        return DB::table('task_categories')
            ->whereNull('deleted_at')
            ->get();
    }

    // Find one category
    public static function findById($id)
    {
        return DB::table('task_categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    // Create a category
    public static function createCategory(array $data)
    {
        return DB::table('task_categories')->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'],
            'is_active' => $data['is_active'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Update a category
    public static function updateCategory($id, array $data)
    {
        return DB::table('task_categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_active' => $data['is_active'],
                'updated_at' => now(),
            ]);
    }

    // Soft-delete a category
    public static function deleteCategory($id)
    {
        return DB::table('task_categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }

    // Check whether a non-deleted category exists
    public static function existsById($id)
    {
        return DB::table('task_categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }
}
