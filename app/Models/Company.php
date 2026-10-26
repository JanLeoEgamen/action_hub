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
        return DB::table('companies')->where('id', $id)->update(['deleted_at' => now()]);
    }

    public static function existsById($id)
    {
        return DB::table('companies')->where('id', $id)->whereNull('deleted_at')->exists();
    }
}