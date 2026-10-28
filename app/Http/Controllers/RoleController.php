<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:create roles', only: ['create', 'store']),
            new Middleware('permission:edit roles', only: ['edit', 'update']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }

    // Display page and DataTable data
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')
                ->orderBy('name', 'ASC')
                ->get();

            return DataTables::collection($roles)
                ->addColumn('permissions', function ($role) {
                    $resourceTitles = [
                        'companies' => 'Companies',
                        'departments' => 'Departments',
                        'groups' => 'Groups',
                        'users' => 'Users',
                        'roles' => 'Roles',
                        'task categories' => 'Task Categories',
                        'projects' => 'Projects',
                        'tasks' => 'Tasks',
                        'tickets' => 'Tickets',
                    ];

                    $grouped = $role->permissions->groupBy(function ($p) {
                        $parts = explode(' ', $p->name, 2);
                        return $parts[1] ?? $parts[0];
                    });

                    $badges = $grouped->map(function ($resourcePerms, $resource) use ($resourceTitles) {
                        $label = $resourceTitles[$resource] ?? ucfirst($resource);
                        $count = $resourcePerms->count();

                        return '<span class="badge bg-light text-dark border me-1 mb-1"'
                            . ' title="' . $count . ' permissions for ' . $label . '"'
                            . '>' . $label . '</span>';
                    })->implode('');

                    return $badges ?: '<span class="text-muted">—</span>';
                })
                ->addColumn('created_at', function ($role) {
                    return $role->created_at
                        ? Carbon::parse($role->created_at)->format('Y-m-d')
                        : '';
                })
                ->addColumn('action', function ($role) {
                    $buttons = '';

                    if (auth()->user()->can('edit roles')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $role->id . '"
                                class="btn btn-success btn-sm edit-role">
                                Edit
                            </button> ';
                    }

                    if (auth()->user()->can('delete roles')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $role->id . '"
                                class="btn btn-danger btn-sm delete-role">
                                Delete
                            </button>';
                    }

                    return $buttons ?: '—';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'permissions'])
                ->make(true);
        }

        $groupedPermissions = Permission::orderBy('name', 'ASC')->get()
            ->groupBy(function ($permission) {
                $parts = explode(' ', $permission->name, 2);
                return $parts[1] ?? $parts[0];
            });

        return view('roles.index', compact('groupedPermissions'));
    }

    // Return reference data for creating a role.
    public function create()
    {
        return response()->json([
            'permissions' => Permission::orderBy('name', 'ASC')->get(),
        ]);
    }

    // Return role data and assigned permissions for editing
    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 404);
        }

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->values(),
        ]);
    }

    // Create role
    public function store(Request $request)
    {
        $guard = config('auth.defaults.guard');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'between:4,30',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $guard),
            ],
            'permission' => 'sometimes|array',
            'permission.*' => [
                'required',
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', $guard),
            ],
        ], [
            'name.required' => 'Please enter the role name.',
            'name.between' => 'The role name must be between 4 and 30 characters.',
            'name.unique' => 'This role name already exists.',
            'permission.array' => 'Invalid permissions submitted.',
            'permission.*.exists' => 'One or more selected permissions are invalid.',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $guard,
        ]);

        $role->syncPermissions($validated['permission'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully.',
        ]);
    }

    // Update role
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'between:4,30',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $role->guard_name)
                    ->ignore($role->id),
            ],
            'permission' => 'sometimes|array',
            'permission.*' => [
                'required',
                'string',
                Rule::exists('permissions', 'name')
                    ->where('guard_name', $role->guard_name),
            ],
        ], [
            'name.required' => 'Please enter the role name.',
            'name.between' => 'The role name must be between 4 and 30 characters.',
            'name.unique' => 'This role name already exists.',
            'permission.array' => 'Invalid permissions submitted.',
            'permission.*.exists' => 'One or more selected permissions are invalid.',
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        // An empty selection removes all previously assigned permissions.
        $role->syncPermissions($validated['permission'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully.',
        ]);
    }

    // Delete role
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Role not found.',
            ], 404);
        }

        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully.',
        ]);
    }
}