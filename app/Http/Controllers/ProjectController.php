<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class ProjectController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view projects',   only: ['index', 'list', 'show']),
            new Middleware('permission:create projects', only: ['store']),
            new Middleware('permission:edit projects',   only: ['show', 'update']),
            new Middleware('permission:delete projects', only: ['destroy']),
        ];
    }

    // Project list page
    public function index()
    {
        return view('projects.index', [
            'users' => Project::getUsersForSelection(),
        ]);
    }

    // AJAX: filtered project list
    public function list(Request $request)
    {
        $validated = $request->validate([
            'filter'    => ['nullable', Rule::in(['all', 'owned_by_me', 'member_of'])],
            'search'    => ['nullable', 'string', 'max:255'],
            'status'    => ['nullable', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'is_active' => ['nullable', Rule::in(['all', '1', '0', 1, 0])],
        ]);

        $projects = Project::getProjectList($validated, auth()->id());

        return response()->json([
            'status'   => 'success',
            'projects' => $projects,
        ]);
    }

    // Return one project for edit modal
    public function show($id = null)
    {
        if (! $id) {
            return response()->json([]);
        }

        $project = Project::getProjectForEdit($id);

        if (! $project) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        return response()->json($project);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        Project::createProject($validated, auth()->id());

        return response()->json([
            'status'  => 'success',
            'message' => 'Project created successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! Project::existsById($id)) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        $validated = $this->validateProject($request);

        Project::updateProject($id, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        if (! Project::existsById($id)) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Project not found.',
            ], 404);
        }

        Project::deleteProject($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project deleted successfully.',
        ]);
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|between:3,255',
            'description' => 'nullable|string',
            'status'      => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'member_ids'  => 'nullable|array',
            'member_ids.*'=> 'integer|exists:users,id',
            'is_active'   => 'nullable|boolean',
        ]);
    }
}