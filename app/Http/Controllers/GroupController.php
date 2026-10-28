<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class GroupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view groups', only: ['index']),
            new Middleware('permission:create groups', only: ['create', 'store']),
            new Middleware('permission:edit groups', only: ['edit', 'update']),
            new Middleware('permission:delete groups', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(Group::getAll())
                ->addColumn('created_at', fn($group) => Carbon::parse($group->created_at)->format('Y-m-d'))
                ->addColumn('is_active', fn($group) => $group->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($group) {
                    $buttons = '';
                    if (auth()->user()->can('edit groups')) {
                        $buttons .= '<button type="button" data-id="'.$group->id.'" class="btn btn-success btn-sm edit-group">Edit</button> ';
                    }
                    if (auth()->user()->can('delete groups')) {
                        $buttons .= '<button type="button" data-id="'.$group->id.'" class="btn btn-danger btn-sm delete-group">Delete</button>';
                    }
                    return $buttons ?: '—';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        $departments = Department::getAll();
        return view('groups.index', compact('departments'));
    }

    // Return reference data for creating a group.
    public function create()
    {
        return response()->json([
            'departments' => Department::getAll(),
        ]);
    }

    public function edit($id)
    {
        $group = Group::findById($id);
        if (!$group) return response()->json(['message' => 'Group not found.'], 404);
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $validated = $this->validateGroup($request);

        Group::createGroup($validated);

        return response()->json(['status' => 'success', 'message' => 'Group created successfully.']);
    }

    public function update(Request $request, $id)
    {
        if (!Group::existsById($id)) return response()->json(['message' => 'Group not found.'], 404);

        $validated = $this->validateGroup($request);

        Group::updateGroup($id, $validated);

        return response()->json(['status' => 'success', 'message' => 'Group updated successfully.']);
    }

    private function validateGroup(Request $request): array
    {
        return $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'group_name' => 'required|string|max:255',
            'group_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
    }

    public function destroy($id)
    {
        if (!Group::existsById($id)) 
            return response()->json(['status' => 'failed', 'message' => 'Group not found.'], 404);

        Group::deleteGroup($id);

        return response()->json(['status' => 'success', 'message' => 'Group deleted successfully.']);
    }
}