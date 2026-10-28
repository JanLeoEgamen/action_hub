<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:create users', only: ['create', 'store']),
            new Middleware('permission:edit users', only: ['edit', 'update']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    // Display page and DataTable data
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(User::getAll())
                ->addColumn('created_at', function ($user) {
                    return $user->created_at
                        ? Carbon::parse($user->created_at)->format('Y-m-d')
                        : '';
                })
                ->addColumn('action', function ($user) {
                    $buttons = '';

                    if (auth()->user()->can('edit users')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $user->id . '"
                                class="btn btn-success btn-sm edit-user">
                                Edit
                            </button> ';
                    }

                    if (auth()->user()->can('delete users')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $user->id . '"
                                class="btn btn-danger btn-sm delete-user">
                                Delete
                            </button>';
                    }

                    return $buttons ?: '—';
                })                
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        $groups = Group::getAll();
        $roles = Role::orderBy('name', 'ASC')->get();
        return view('users.index', compact('roles', 'groups'));
    }

    // Return reference data for creating a user.
    public function create()
    {
        return response()->json([
            'roles' => Role::orderBy('name', 'ASC')->get(),
            'groups' => Group::getAll(),
        ]);
    }

    // Return user data for editing.
    public function edit($id)
    {
        $user = User::findById($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json($user);
    }

    // Create a user
    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        $userId = User::createUser($validated);

        $user = User::find($userId);

        $roles = Role::whereIn('id', $validated['roles'])->get();

        $user->syncRoles($roles);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully.',
        ]);
    }

    // Update a user
    public function update(Request $request, $id)
    {
        if (!User::existsById($id)) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $validated = $this->validateUser($request, $id);

        User::updateUser($id, $validated);

        $user = User::find($id);

        $roles = Role::whereIn('id', $validated['roles'])->get();

        $user->syncRoles($roles);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
        ]);
    }

    private function validateUser(Request $request, $id = null): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|between:4,30',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->when($id, fn ($rule) => $rule->ignore($id)),
            ],
            'password' => $id ? 'nullable|string|min:8|same:confirm_password' : 'required|string|min:8|same:confirm_password',
            'confirm_password' => $id ? 'required_with:password|nullable|string' : 'required|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|integer|distinct|exists:roles,id',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);
    }

    // Delete a user
    public function destroy($id)
    {
        if (!User::existsById($id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found.',
            ], 404);
        }

        User::deleteUser($id);

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.',
        ]);
    }
}