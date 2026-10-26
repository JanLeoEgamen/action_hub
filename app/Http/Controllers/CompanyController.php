<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class CompanyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view companies', only: ['index']),
            new Middleware('permission:create companies', only: ['store']),
            new Middleware('permission:edit companies', only: ['show', 'update']),
            new Middleware('permission:delete companies', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(Company::getAll())
                ->addColumn('created_at', fn($company) => Carbon::parse($company->created_at)->format('Y-m-d'))
                ->addColumn('is_active', fn($company) => $company->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($company) {
                    $buttons = '';
                    if (auth()->user()->can('edit companies')) {
                        $buttons .= '<button type="button" data-id="'.$company->id.'" class="btn btn-success btn-sm edit-company">Edit</button> ';
                    }
                    if (auth()->user()->can('delete companies')) {
                        $buttons .= '<button type="button" data-id="'.$company->id.'" class="btn btn-danger btn-sm delete-company">Delete</button>';
                    }
                    return $buttons ?: '—';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('companies.index');
    }

    public function show($id)
    {
        $company = Company::findById($id);
        if (!$company) return response()->json(['message' => 'Company not found.'], 404);
        return response()->json($company);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Company::createCompany($validated);

        return response()->json(['status' => 'success', 'message' => 'Company created successfully.']);
    }

    public function update(Request $request, $id)
    {
        if (!Company::existsById($id)) return response()->json(['message' => 'Company not found.'], 404);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Company::updateCompany($id, $validated);

        return response()->json(['status' => 'success', 'message' => 'Company updated successfully.']);
    }

    public function destroy($id)
    {
        if (!Company::existsById($id)) return response()->json(['status' => 'failed', 'message' => 'Company not found.'], 404);

        Company::deleteCompany($id);

        return response()->json(['status' => 'success', 'message' => 'Company deleted successfully.']);
    }
}