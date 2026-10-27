<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'department_name', 'department_description', 'is_active'];

    public static function getAll()
    {
        return DB::table('departments')
            ->join('companies', 'departments.company_id', '=', 'companies.id')
            ->whereNull('departments.deleted_at')
            ->select('departments.*', 'companies.company_name')
            ->orderByDesc('departments.created_at')
            ->get();
    }

    public static function findById($id)
    {
        return DB::table('departments')->where('id', $id)->whereNull('deleted_at')->first();
    }

    public static function createDepartment(array $data)
    {
        return DB::table('departments')->insertGetId([
            'company_id' => $data['company_id'],
            'department_name' => $data['department_name'],
            'department_description' => $data['department_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function updateDepartment($id, array $data)
    {
        return DB::table('departments')->where('id', $id)->update([
            'company_id' => $data['company_id'],
            'department_name' => $data['department_name'],
            'department_description' => $data['department_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'updated_at' => now(),
        ]);
    }

    public static function deleteDepartment($id)
    {
        return DB::table('departments')->where('id', $id)->update(['deleted_at' => now()]);
    }

    public static function existsById($id)
    {
        return DB::table('departments')->where('id', $id)->whereNull('deleted_at')->exists();
    }

    public static function getDeptCounts()
    {
        return DB::table('departments')
            ->join('companies', 'companies.id', '=', 'departments.company_id')
            ->select('companies.company_name', 'departments.department_name', DB::raw('count(*) as total'))
            ->whereNull('departments.deleted_at')
            ->whereNull('companies.deleted_at')
            ->groupBy('companies.id', 'departments.id')
            ->get();
    }

    public static function getCount(): int
    {
        return DB::table('departments')->whereNull('deleted_at')->count();
    }
}