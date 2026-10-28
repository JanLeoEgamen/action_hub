<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class DepartmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view departments', only: ['index']),
            new Middleware('permission:create departments', only: ['create', 'store']),
            new Middleware('permission:edit departments', only: ['edit', 'update']),
            new Middleware('permission:delete departments', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(Department::getAll())
                ->addColumn('created_at', fn($dept) => Carbon::parse($dept->created_at)->format('Y-m-d'))
                ->addColumn('department_description', fn($dept) => $dept->department_description)
                ->addColumn('is_active', fn($dept) => $dept->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($dept) {
                    $buttons = '';
                    if (auth()->user()->can('edit departments')) {
                        $buttons .= '<button type="button" data-id="'.$dept->id.'" class="btn btn-success btn-sm edit-department">Edit</button> ';
                    }
                    if (auth()->user()->can('delete departments')) {
                        $buttons .= '<button type="button" data-id="'.$dept->id.'" class="btn btn-danger btn-sm delete-department">Delete</button>';
                    }
                    return $buttons ?: '—';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        $companies = Company::getAll();
        return view('departments.index', compact('companies'));
    }

    // Return reference data for creating a department.
    public function create()
    {
        return response()->json([
            'companies' => Company::getAll(),
        ]);
    }

    public function edit($id)
    {
        $dept = Department::findById($id);
        if (!$dept) return response()->json(['message' => 'Department not found.'], 404);
        return response()->json($dept);
    }

    public function store(Request $request)
    {
        $validated = $this->validateDepartment($request);

        Department::createDepartment($validated);

        return response()->json(['status' => 'success', 'message' => 'Department created successfully.']);
    }

    public function update(Request $request, $id)
    {
        if (!Department::existsById($id)) return response()->json(['message' => 'Department not found.'], 404);

        $validated = $this->validateDepartment($request);

        Department::updateDepartment($id, $validated);

        return response()->json(['status' => 'success', 'message' => 'Department updated successfully.']);
    }

    private function validateDepartment(Request $request): array
    {
        return $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'department_name' => 'required|string|max:255',
            'department_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
    }

    public function destroy($id)
    {
        if (!Department::existsById($id)) 
            return response()
        ->json(['status' => 'failed', 'message' => 'Department not found.'], 404);

        Department::deleteDepartment($id);

        return response()->json(['status' => 'success', 'message' => 'Department deleted successfully.']);
    }
}