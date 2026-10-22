<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class TaskCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view task categories', only: ['index']),
            new Middleware('permission:create task categories', only: ['store']),
            new Middleware('permission:edit task categories', only: ['show', 'update']),
            new Middleware('permission:delete task categories', only: ['destroy']),
        ];
    }

    // Display page and DataTable data
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::collection(TaskCategory::getAll())
                ->addColumn('created_at', function ($category) {
                    return $category->created_at
                        ? Carbon::parse($category->created_at)->format('Y-m-d')
                        : '';
                })
                ->addColumn('is_active', function ($category) {
                    return $category->is_active
                        ? 'Active'
                        : 'Inactive';
                })
                ->addColumn('action', function ($category) {
                    $buttons = '';

                    if (auth()->user()->can('edit task categories')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $category->id . '"
                                class="btn btn-success btn-sm edit-taskCategory">
                                Edit
                            </button> ';
                    }

                    if (auth()->user()->can('delete task categories')) {
                        $buttons .= '
                            <button type="button"
                                data-id="' . $category->id . '"
                                class="btn btn-danger btn-sm delete-taskCategory">
                                Delete
                            </button>';
                    }

                    return $buttons ?: '—';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('task-categories.index');
    }

    // Return category data for editing; null ID means create
    public function show($id = null)
    {
        if (!$id) {
            return response()->json([]);
        }

        $category = TaskCategory::findById($id);

        if (!$category) {
            return response()->json([
                'message' => 'Task category not found.',
            ], 404);
        }

        return response()->json($category);
    }

    // Create category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|between:4,30',
            'description' => 'required|string|between:4,30',
            'is_active' => 'required|boolean',
        ]);

        TaskCategory::createCategory($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Task category created successfully.',
        ]);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|between:4,30',
            'description' => 'required|string|between:4,30',
            'is_active' => 'required|boolean',
        ]);

        if (!TaskCategory::existsById($id)) {
            return response()->json([
                'message' => 'Task category not found.',
            ], 404);
        }

        TaskCategory::updateCategory($id, $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Task category updated successfully.',
        ]);
    }

    // Soft-delete category
    public function destroy($id)
    {
        if (!TaskCategory::existsById($id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Task category not found.',
            ], 404);
        }

        TaskCategory::deleteCategory($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Task category deleted successfully.',
        ]);
    }
}
