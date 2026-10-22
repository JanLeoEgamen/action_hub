<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view tasks', only: ['index', 'board', 'show']),
            new Middleware('permission:create tasks', only: ['store']),
            new Middleware('permission:edit tasks', only: ['show', 'update']),
            new Middleware('permission:delete tasks', only: ['destroy']),
        ];
    }

    // Kanban board page
    public function index()
    {
        return view('tasks.index', [
            'categories' => Task::getActiveCategories(),
            'users'      => Task::getUsersForAssignment(),
        ]);
    }

    // AJAX: filtered board data
    public function board(Request $request)
    {
        $validated = $request->validate([
            'filter'      => ['nullable', Rule::in(['all', 'assigned_by_me', 'assigned_to_me'])],
            'search'      => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'status'      => ['nullable', Rule::in(['pending', 'in_progress', 'completed'])],
            'overdue'     => ['nullable', 'in:1,0'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $tasks = Task::getBoardTasks($validated, auth()->id());

        return response()->json([
            'status' => 'success',
            'tasks'  => $tasks,
        ]);
    }

    // Return one task for the edit modal
    public function show($id = null)
    {
        if (! $id) {
            return response()->json([]);
        }

        $task = Task::getTaskForEdit($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        return response()->json($task);
    }

    // Create task
    public function store(Request $request)
    {
        $validated = $this->validateTask($request);

        Task::createTask($validated, auth()->id());

        return response()->json([
            'status'  => 'success',
            'message' => 'Task created successfully.',
        ]);
    }

    // Update task
    public function update(Request $request, $id)
    {
        if (! Task::existsById($id)) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $validated = $this->validateTask($request);

        Task::updateTask($id, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task updated successfully.',
        ]);
    }

    // Update only the status (drag & drop)
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
        ]);

        if (! Task::existsById($id)) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        Task::updateStatus($id, $validated['status']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task status updated successfully.',
        ]);
    }

    // Soft delete
    public function destroy($id)
    {
        if (! Task::existsById($id)) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Task not found.',
            ], 404);
        }

        Task::deleteTask($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task deleted successfully.',
        ]);
    }

    
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title'          => 'required|string|between:3,255',
            'description'    => 'nullable|string',
            'category_id'    => [
                'required',
                'integer',
                Rule::exists('task_categories', 'id')->where(function ($q) {
                    $q->whereNull('deleted_at')->where('is_active', 1);
                }),
            ],
            'assignee_ids'   => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id',
            'due_date'       => 'nullable|date',
            'status'         => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'is_active'      => 'nullable|boolean',
        ]);
    }
}