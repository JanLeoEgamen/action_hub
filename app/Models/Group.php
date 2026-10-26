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
}