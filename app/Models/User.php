<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(
            Task::class,
            'task_assignees',
            'user_id',
            'task_id'
        )
            ->withPivot(['id', 'is_active', 'deleted_at'])
            ->withTimestamps();
    }

    // Get all users with their role names
    public static function getAll()
    {
        return DB::table('users')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where(
                        'model_has_roles.model_type',
                        '=',
                        self::class
                    );
            })
            ->leftJoin(
                'roles',
                'model_has_roles.role_id',
                '=',
                'roles.id'
            )
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.created_at',
                DB::raw(
                    "GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ', ') as roles"
                )
            )
            ->groupBy(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.created_at'
            )
            ->orderByDesc('users.created_at')
            ->get();
    }

    // Find one user and retrieve the assigned role ID.
    public static function findById($id)
    {
        return DB::table('users')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where(
                        'model_has_roles.model_type',
                        '=',
                        self::class
                    );
            })
            ->leftJoin(
                'roles',
                'model_has_roles.role_id',
                '=',
                'roles.id'
            )
            ->where('users.id', $id)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'roles.id as role_id'
            )
            ->first();
    }

    // Create a user.
    public static function createUser(array $data)
    {
        return DB::table('users')->insertGetId([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Update a user
    public static function updateUser($id, array $data)
    {
        $userData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'updated_at' => now(),
        ];

        // Change the password only if a new one is provided
        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        return DB::table('users')
            ->where('id', $id)
            ->update($userData);
    }

    // Delete a user
    public static function deleteUser($id)
    {
        return DB::table('users')
            ->where('id', $id)
            ->delete();
    }

    // Check whether a user exists
    public static function existsById($id)
    {
        return DB::table('users')
            ->where('id', $id)
            ->exists();
    }

    // Check whether an email already exists
    public static function emailExists($email, $exceptId = null)
    {
        $query = DB::table('users')
            ->where('email', $email);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}