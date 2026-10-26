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
            new Middleware('permission:create groups', only: ['store']),
            new Middleware('permission:edit groups', only: ['show', 'update']),
            new Middleware('permission:delete groups', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(Group::getAll())
                ->addColumn('created_at', fn($group) => Carbon::parse($group->created_at)->format('Y-m-d'))
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
                ->rawColumns(['action'])
                ->make(true);
        }

        $departments = Department::getAll();
        return view('groups.index', compact('departments'));
    }

    public function show($id)
    {
        $group = Group::findById($id);
        if (!$group) return response()->json(['message' => 'Group not found.'], 404);
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'group_name' => 'required|string|max:255',
            'group_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Group::createGroup($validated);

        return response()->json(['status' => 'success', 'message' => 'Group created successfully.']);
    }

    public function update(Request $request, $id)
    {
        if (!Group::existsById($id)) return response()->json(['message' => 'Group not found.'], 404);

        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'group_name' => 'required|string|max:255',
            'group_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Group::updateGroup($id, $validated);

        return response()->json(['status' => 'success', 'message' => 'Group updated successfully.']);
    }

    public function destroy($id)
    {
        if (!Group::existsById($id)) 
            return response()->json(['status' => 'failed', 'message' => 'Group not found.'], 404);

        Group::deleteGroup($id);

        return response()->json(['status' => 'success', 'message' => 'Group deleted successfully.']);
    }
}