<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return[
            new Middleware('permission:view task categories', only: ['index']),
            new Middleware('permission:create task categories', only: ['create']),
            new Middleware('permission:edit task categories', only: ['edit']),
            new Middleware('permission:delete task categories', only: ['destroy'])
        ];
    }

    public function index()
    {
        $taskCategories = TaskCategory::orderBy('created_at')
            ->paginate(10);

        return view('task-categories.index', [
            'taskCategories' => $taskCategories
        ]);
    }

    public function create()
    {
        return view('task-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|between:4,30',
            'description' => 'required|between:4,30',
            'is_active' => 'required|boolean'
        ], [
            'name.required' => 'Please enter the category name',
            'name.between' => 'Please enter characters between 4 to 20',           
            'description.required' => 'Please enter characters between 4 to 20',
            'description.between' => 'Please enter characters between 4 to 20'
        ]);

        TaskCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('task-categories.index')->with('success', 'Task Category Added Successfully');

    }

    public function edit(TaskCategory $taskCategory)
    {

        return view('task-categories.edit', compact('taskCategory'));
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $validated = $request->validate([
            'name' => 'required|between:4,30',
            'description' => 'required|between:4,30',
            'is_active' => 'required|boolean'
        ], [
            'name.required' => 'Please enter the category name',
            'name.between' => 'Please enter characters between 4 to 20',           
            'description.required' => 'Please category description',
            'description.between' => 'Please enter characters between 4 to 20'
        ]);
        
        $taskCategory->update($validated);
        return redirect()->route('task-categories.index')->with('success', 'Task Category Updated Successfully');

    }

    public function destroy(TaskCategory $taskCategory)
    {
        $taskCategory->delete();
        return redirect()->route('task-categories.index')->with('success', 'Task Category deleted successfully');

    }
}
