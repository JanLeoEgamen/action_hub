<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'group_name', 'group_description', 'is_active'];

    public static function getAll()
    {
        return DB::table('groups')
            ->join('departments', 'groups.department_id', '=', 'departments.id')
            ->join('companies', 'departments.company_id', '=', 'companies.id')
            ->whereNull('groups.deleted_at')
            ->select('groups.*', 'departments.department_name', 'companies.company_name')
            ->orderByDesc('groups.created_at')
            ->get();
    }

    public static function getForSelection()
    {
        return DB::table('groups')
            ->join('departments', 'groups.department_id', '=', 'departments.id')
            ->join('companies', 'departments.company_id', '=', 'companies.id')
            ->whereNull('groups.deleted_at')
            ->whereNull('departments.deleted_at')
            ->whereNull('companies.deleted_at')
            ->select('groups.*', 'departments.department_name', 'companies.company_name')
            ->orderByDesc('groups.created_at')
            ->get();
    }

    public static function findById($id)
    {
        return DB::table('groups')->where('id', $id)->whereNull('deleted_at')->first();
    }

    public static function createGroup(array $data)
    {
        return DB::table('groups')->insertGetId([
            'department_id' => $data['department_id'],
            'group_name' => $data['group_name'],
            'group_description' => $data['group_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function updateGroup($id, array $data)
    {
        return DB::table('groups')->where('id', $id)->update([
            'department_id' => $data['department_id'],
            'group_name' => $data['group_name'],
            'group_description' => $data['group_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'updated_at' => now(),
        ]);
    }

    public static function deleteGroup($id)
    {
        return DB::table('groups')->where('id', $id)->update(['deleted_at' => now()]);
    }

    public static function existsById($id)
    {
        return DB::table('groups')->where('id', $id)->whereNull('deleted_at')->exists();
    }

    public static function getGroupUserCounts()
    {
        return DB::table('groups')
            ->join('departments', 'departments.id', '=', 'groups.department_id')
            ->join('companies', 'companies.id', '=', 'departments.company_id')
            ->leftJoin('users', 'users.group_id', '=', 'groups.id')
            ->select('companies.company_name', 'departments.department_name', 'groups.group_name', DB::raw('count(DISTINCT users.id) as total'))
            ->whereNull('groups.deleted_at')
            ->whereNull('departments.deleted_at')
            ->whereNull('companies.deleted_at')
            ->groupBy('companies.id', 'departments.id', 'groups.id')
            ->get();
    }

    public static function getCount(): int
    {
        return DB::table('groups')->whereNull('deleted_at')->count();
    }
}