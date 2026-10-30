<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['company_name', 'company_description', 'is_active'];

    public static function getAll()
    {
        return DB::table('companies')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    public static function findById($id)
    {
        return DB::table('companies')->where('id', $id)->whereNull('deleted_at')->first();
    }

    public static function createCompany(array $data)
    {
        return DB::table('companies')->insertGetId([
            'company_name' => $data['company_name'],
            'company_description' => $data['company_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public static function updateCompany($id, array $data)
    {
        return DB::table('companies')->where('id', $id)->update([
            'company_name' => $data['company_name'],
            'company_description' => $data['company_description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'updated_at' => now(),
        ]);
    }

    public static function deleteCompany($id)
    {
        return DB::transaction(function () use ($id) {
            // Soft delete the company
            DB::table('companies')->where('id', $id)->update(['deleted_at' => now()]);

            // Deactivate all departments of this company
            DB::table('departments')
                ->where('company_id', $id)
                ->whereNull('deleted_at')
                ->update(['is_active' => 0, 'updated_at' => now()]);

            // Deactivate all groups under departments of this company
            DB::table('groups')
                ->whereIn('department_id', function ($q) use ($id) {
                    $q->select('id')->from('departments')->where('company_id', $id);
                })
                ->whereNull('deleted_at')
                ->update(['is_active' => 0, 'updated_at' => now()]);
        });
    }

    public static function existsById($id)
    {
        return DB::table('companies')->where('id', $id)->whereNull('deleted_at')->exists();
    }

    public static function getUserCounts()
    {
        return DB::table('companies')
            ->leftJoin('departments', 'departments.company_id', '=', 'companies.id')
            ->leftJoin('groups', 'groups.department_id', '=', 'departments.id')
            ->leftJoin('users', 'users.group_id', '=', 'groups.id')
            ->select('companies.company_name', DB::raw('count(DISTINCT users.id) as total'))
            ->whereNull('companies.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('companies.id', 'companies.company_name')
            ->get();
    }

    public static function getCount(): int
    {
        return DB::table('companies')->whereNull('deleted_at')->count();
    }
}