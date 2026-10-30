<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view projects',   only: ['index', 'list']),
            new Middleware('permission:create projects', only: ['create', 'store']),
            new Middleware('permission:edit projects',   only: ['edit', 'update']),
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

    // Return reference data for creating a project.
    public function create()
    {
        return response()->json([]);
    }

    // Return one project for edit modal
    public function edit($id)
    {
        $project = Project::getProjectForEdit($id);

        if (! $project) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        return response()->json($project);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        $projectId = Project::createProject($validated, auth()->id());

        return response()->json([
            'status'  => 'success',
            'message' => 'Project created successfully.',
            'project' => ['id' => $projectId],
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! Project::existsById($id)) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        $project = Project::findById($id);

        $validated = $this->validateProject($request, $project);

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

    private function validateProject(Request $request, $existingProject = null): array
    {
        $validated = $request->validate([
            'name'        => 'required|string|between:3,255',
            'description' => 'nullable|string',
            'status'      => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'member_ids'  => 'nullable|array',
            'member_ids.*'=> 'integer|exists:users,id',
            'is_active'   => 'nullable|boolean',
        ]);

        // Start date cannot be earlier than today.
        $startDate = $request->input('start_date');
        if ($startDate) {
            $today = now()->toDateString();

            $isChanged = ($existingProject === null)
                ? true
                : $startDate !== ($existingProject->start_date ?? null);

            if ($isChanged && $startDate < $today) {
                throw ValidationException::withMessages([
                    'start_date' => ['The start date cannot be earlier than today.'],
                ]);
            }
        }

        return $validated;
    }
}