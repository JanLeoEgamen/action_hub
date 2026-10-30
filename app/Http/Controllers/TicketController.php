<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view projects',   only: ['index', 'board']),
            new Middleware('permission:create tickets',  only: ['create', 'store']),
            new Middleware('permission:edit tickets',    only: ['edit', 'update', 'updateStatus']),
            new Middleware('permission:delete tickets',  only: ['destroy']),
        ];
    }

    // Kanban board for a project
    public function index($projectId)
    {
        if (! Project::existsById($projectId)) {
            abort(404);
        }

        $project = Project::findById($projectId);

        return view('tickets.index', [
            'project' => $project,
            'users'   => Ticket::getUsersForAssignment((int) $projectId),
        ]);
    }

    // AJAX: filtered board data
    public function board(Request $request, $projectId)
    {
        if (! Project::existsById($projectId)) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        $validated = $request->validate([
            'search'   => ['nullable', 'string', 'max:255'],
            'label'    => ['nullable', Rule::in(['bug', 'feature', 'improvement', 'task'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status'   => ['nullable', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
            'overdue'  => ['nullable', 'in:1,0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $tickets = Ticket::getBoardTickets((int) $projectId, $validated);

        return response()->json([
            'status'  => 'success',
            'tickets' => $tickets,
        ]);
    }

    // Return reference data for creating a ticket.
    public function create()
    {
        return response()->json([]);
    }

    public function edit($id)
    {
        $ticket = Ticket::getTicketForEdit($id);

        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found.'], 404);
        }

        return response()->json($ticket);
    }

    public function store(Request $request, $projectId)
    {
        if (! Project::existsById($projectId)) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        $validated = $this->validateTicket($request, (int) $projectId);

        $this->ensureAssigneesAreProjectMembers((int) $projectId, $validated['assignee_ids'] ?? []);

        Ticket::createTicket($validated, (int) $projectId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket created successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! Ticket::existsById($id)) {
            return response()->json(['message' => 'Ticket not found.'], 404);
        }

        $ticket = Ticket::findById($id);
        $projectId = $ticket->project_id;

        $validated = $this->validateTicket($request, (int) $projectId, $ticket);

        $this->ensureAssigneesAreProjectMembers((int) $projectId, $validated['assignee_ids'] ?? []);

        Ticket::updateTicket($id, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket updated successfully.',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
        ]);

        if (! Ticket::existsById($id)) {
            return response()->json(['message' => 'Ticket not found.'], 404);
        }

        Ticket::updateStatus($id, $validated['status']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket status updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        if (! Ticket::existsById($id)) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Ticket not found.',
            ], 404);
        }

        Ticket::deleteTicket($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket deleted successfully.',
        ]);
    }

    private function validateTicket(Request $request, int $projectId = null, $existingTicket = null): array
    {
        $validated = $request->validate([
            'title'       => 'required|string|between:3,255',
            'description' => 'nullable|string',
            'label'       => ['required', Rule::in(['bug', 'feature', 'improvement', 'task'])],
            'priority'    => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status'      => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
            'due_date'    => 'nullable|date',
            'is_active'   => 'nullable|boolean',
            'assignee_ids'   => 'nullable|array',
            'assignee_ids.*' => 'integer|exists:users,id',
        ]);

        // Due date must fall within the project start and end dates (if set).
        $dueDate = $request->input('due_date');
        if ($dueDate && $projectId) {
            $project = Project::findById($projectId);
            if ($project) {
                $startDate = $project->start_date ?? null;
                $endDate   = $project->end_date   ?? null;

                // Determine if the due date is being changed (create = always changed when provided).
                $isChanged = ($existingTicket === null)
                    ? true
                    : $dueDate !== (($existingTicket->due_date ?? null) instanceof \Carbon\Carbon
                        ? ($existingTicket->due_date ?? null)->format('Y-m-d')
                        : ($existingTicket->due_date ?? null));

                if ($isChanged) {
                    if ($startDate && $dueDate < $startDate) {
                        throw ValidationException::withMessages([
                            'due_date' => ['The due date cannot be earlier than the project start date (' . $startDate . ').'],
                        ]);
                    }
                    if ($endDate && $dueDate > $endDate) {
                        throw ValidationException::withMessages([
                            'due_date' => ['The due date cannot be later than the project end date (' . $endDate . ').'],
                        ]);
                    }
                }
            }
        }

        return $validated;
    }

    /**
     * Ensure all given assignee IDs are active members of the project.
     * Throws a ValidationException (422) if any assignee is not a project member.
     */
    private function ensureAssigneesAreProjectMembers(int $projectId, array $assigneeIds): void
    {
        if (empty($assigneeIds)) {
            return;
        }

        $memberUserIds = ProjectMember::where('project_id', $projectId)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->pluck('user_id')
            ->all();

        $invalid = array_diff($assigneeIds, $memberUserIds);
        if (! empty($invalid)) {
            throw new \Illuminate\Validation\ValidationException(
                validator()->make(
                    ['assignee_ids' => $assigneeIds],
                    ['assignee_ids.*' => 'Selected assignees must be active members of this project.']
                )
            );
        }
    }
}