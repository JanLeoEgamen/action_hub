<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="{{ $project->name }}"
            action-label="Create"
            action-id="create-ticket"
            permission="create tickets"
        />
    </x-slot>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                    &larr; Back to Projects
                </a>
            </div>
        </div>

        <div id="successMessage" class="alert alert-success d-none"></div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" id="search" class="form-control" placeholder="Search by title...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Label</label>
                        <select id="label" class="form-select">
                            <option value="">All</option>
                            <option value="bug">Bug</option>
                            <option value="feature">Feature</option>
                            <option value="improvement">Improvement</option>
                            <option value="task">Task</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select id="priority" class="form-select">
                            <option value="">All</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="status" class="form-select">
                            <option value="">All</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">Due</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="overdue">
                            <label class="form-check-label" for="overdue">Overdue only</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanban --}}
        <div class="row g-3" id="kanban-board">
            @foreach ([
                'open'        => 'Open',
                'in_progress' => 'In Progress',
                'resolved'    => 'Resolved',
                'closed'      => 'Closed',
            ] as $key => $label)
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ $label }}</span>
                            <span class="badge bg-secondary" data-count="{{ $key }}">0</span>
                        </div>
                        <div class="card-body kanban-column"
                             data-status="{{ $key }}"
                             style="min-height: 300px;">
                            {{-- cards injected by JS --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="create-ticket-modal" tabindex="-1"
        aria-labelledby="createTicketModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-ticket-form">
                    @csrf
                    <input type="hidden" id="create-ticket_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createTicketModalTitle">Create Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="create-title" class="form-label">Title</label>
                            <input type="text" name="title" id="create-title"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="create-description" class="form-label">Description</label>
                            <textarea name="description" id="create-description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="create-label_form" class="form-label">Label</label>
                                <select name="label" id="create-label_form" class="form-select" required>
                                    <option value="task">Task</option>
                                    <option value="bug">Bug</option>
                                    <option value="feature">Feature</option>
                                    <option value="improvement">Improvement</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="create-priority_form" class="form-label">Priority</label>
                                <select name="priority" id="create-priority_form" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="create-status_form" class="form-label">Status</label>
                                <select name="status" id="create-status_form" class="form-select" required>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create-due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="create-due_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="is_active" id="create-is_active" value="1" checked>
                                    <label class="form-check-label" for="create-is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Assignees</label>
                            <div id="create-assignee_ids" class="border rounded p-2"
                                style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input assignee-checkbox"
                                            type="checkbox"
                                            name="assignee_ids[]"
                                            value="{{ $user->id }}"
                                            id="create-assignee_{{ $user->id }}">
                                        <label class="form-check-label" for="create-assignee_{{ $user->id }}">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCreateTicket" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-ticket-modal" tabindex="-1"
        aria-labelledby="editTicketModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit-ticket-form">
                    @csrf
                    <input type="hidden" id="edit-ticket_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editTicketModalTitle">Edit Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Title</label>
                            <input type="text" name="title" id="edit-title"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Description</label>
                            <textarea name="description" id="edit-description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit-label_form" class="form-label">Label</label>
                                <select name="label" id="edit-label_form" class="form-select" required>
                                    <option value="task">Task</option>
                                    <option value="bug">Bug</option>
                                    <option value="feature">Feature</option>
                                    <option value="improvement">Improvement</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit-priority_form" class="form-label">Priority</label>
                                <select name="priority" id="edit-priority_form" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit-status_form" class="form-label">Status</label>
                                <select name="status" id="edit-status_form" class="form-select" required>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="edit-due_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="is_active" id="edit-is_active" value="1" checked>
                                    <label class="form-check-label" for="edit-is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Assignees</label>
                            <div id="edit-assignee_ids" class="border rounded p-2"
                                style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input assignee-checkbox"
                                            type="checkbox"
                                            name="assignee_ids[]"
                                            value="{{ $user->id }}"
                                            id="edit-assignee_{{ $user->id }}">
                                        <label class="form-check-label" for="edit-assignee_{{ $user->id }}">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveEditTicket" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        id="deleteTicketModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this ticket?"
        confirm-button-id="confirmDeleteTicket"
        confirm-label="Delete"
    />

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            $(document).ready(function () {
                const PROJECT_ID = {{ $project->id }};

                const createModal = new bootstrap.Modal(document.getElementById('create-ticket-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-ticket-modal'));

                const STATUS_LABELS = {
                    open: 'Open',
                    in_progress: 'In Progress',
                    resolved: 'Resolved',
                    closed: 'Closed',
                };

                function showSuccess(message) {
                    $('#successMessage').text(message).removeClass('d-none');
                    setTimeout(function () {
                        $('#successMessage').addClass('d-none');
                    }, 3000);
                }

                function escapeHtml(s) {
                    return $('<div>').text(s == null ? '' : s).html();
                }

                // loading board
                function loadBoard() {
                    const params = {
                        search:   $('#search').val(),
                        label:    $('#label').val(),
                        priority: $('#priority').val(),
                        status:   $('#status').val(),
                        overdue:  $('#overdue').is(':checked') ? 1 : 0,
                    };

                    $.ajax({
                        url: '{{ route("projects.tickets.board", ["projectId" => $project->id]) }}',
                        method: 'GET',
                        data: params,
                        success: function (response) {
                            renderBoard(response.tickets || []);
                        },
                        error: function (xhr) {
                            console.error('Board load failed', xhr.status, xhr.responseText);
                            alert('Unable to load tickets.');
                        }
                    });
                }

                // render the board with tickets
                function renderBoard(tickets) {
                    const groups = {
                        open: [],
                        in_progress: [],
                        resolved: [],
                        closed: [],
                    };

                    tickets.forEach(function (ticket) {
                        if (groups.hasOwnProperty(ticket.status)) {
                            groups[ticket.status].push(ticket);
                        }
                    });

                    Object.keys(groups).forEach(function (status) {
                        const $col = $(`.kanban-column[data-status="${status}"]`);
                        $col.empty();

                        const countEl = $col.closest('.card').find('.badge[data-count]');
                        if (countEl.length) {
                            countEl.text(groups[status].length);
                        }

                        groups[status].forEach(function (ticket) {
                            const $card = $(`
                                <div class="card mb-2 ticket-card" data-id="${ticket.id}">
                                    <div class="card-body">
                                        <h6 class="card-title">${escapeHtml(ticket.title)}</h6>
                                        <p class="card-text text-muted small">${escapeHtml(ticket.description || '')}</p>
                                        <small class="text-muted">Label: ${escapeHtml(ticket.label)} | Priority: ${escapeHtml(ticket.priority)}</small><br>
                                        ${ticket.due_date ? `<small class="text-muted">Due: ${escapeHtml(ticket.due_date)}</small><br>` : ''}
                                        ${ticket.assignee_names ? `<small class="text-info">Assignee: ${escapeHtml(ticket.assignee_names)}</small><br>` : ''}
                                        <button type="button" class="btn btn-sm btn-success edit-ticket" data-id="${ticket.id}">Edit</button>
                                        <button type="button" class="btn btn-sm btn-danger delete-ticket" data-id="${ticket.id}">Delete</button>
                                    </div>
                                </div>
                            `);
                            $col.append($card);
                        });
                    });
                }

                function resetCreateForm() {
                    $('#create-ticket-form')[0].reset();
                    $('#create-ticket_id').val('');
                    $('#create-assignee_ids input[type="checkbox"]').prop('checked', false);
                    $('#create-formErrors').addClass('d-none').empty();
                }

                function resetEditForm() {
                    $('#edit-ticket-form')[0].reset();
                    $('#edit-ticket_id').val('');
                    $('#edit-assignee_ids input[type="checkbox"]').prop('checked', false);
                    $('#edit-formErrors').addClass('d-none').empty();
                }

                $('#create-ticket').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                $('#kanban-board').on('click', '.edit-ticket', function (e) {
                    e.preventDefault();
                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);
                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetEditForm();

                    $.ajax({
                        url: `/tickets/${id}/edit`,
                        method: 'GET',
                        success: function (ticket) {
                            $('#edit-ticket_id').val(ticket.id);
                            $('#edit-title').val(ticket.title);
                            $('#edit-description').val(ticket.description);
                            $('#edit-label_form').val(ticket.label);
                            $('#edit-priority_form').val(ticket.priority);
                            $('#edit-status_form').val(ticket.status);
                            $('#edit-due_date').val(ticket.due_date);
                            $('#edit-is_active').prop('checked', !!ticket.is_active);
                            $('#edit-assignee_ids input[type="checkbox"]').prop('checked', false);
                            (ticket.assignee_ids || []).forEach(function (uid) {
                                $('#edit-assignee_ids input[type="checkbox"][value="' + uid + '"]').prop('checked', true);
                            });
                            editModal.show();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to load ticket.');
                        },
                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                $('#create-ticket-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateTicket').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/projects/${PROJECT_ID}/tickets`,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            createModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                Object.values(xhr.responseJSON.errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages += `<div>${message}</div>`;
                                    });
                                });
                                $('#create-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save ticket.');
                            }
                        },
                        complete: function () {
                            $('#saveCreateTicket').prop('disabled', false).text('Save');
                        }
                    });
                });

                $('#edit-ticket-form').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#edit-ticket_id').val();

                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditTicket').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/tickets/${id}`,
                        method: 'PUT',
                        data: $(this).serialize(),
                        success: function (response) {
                            editModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                Object.values(xhr.responseJSON.errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages += `<div>${message}</div>`;
                                    });
                                });
                                $('#edit-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save ticket.');
                            }
                        },
                        complete: function () {
                            $('#saveEditTicket').prop('disabled', false).text('Save');
                        }
                    });
                });

                // Filters
                $('#search, #label, #priority, #status').on('change input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadBoard, 300);
                });
                let searchDebounce;
                $('#overdue').on('change', loadBoard);

                // Delete
                const deleteModalElement = document.getElementById('deleteTicketModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);
                let deleteTicketId = null;

                $('#kanban-board').on('click', '.delete-ticket', function (e) {
                    e.preventDefault();
                    if ($('#confirmDeleteTicket').prop('disabled')) return;
                    deleteTicketId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteTicket').on('click', function () {
                    if (! deleteTicketId) return;

                    const button = $(this);
                    if (button.prop('disabled')) return;
                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/tickets/${deleteTicketId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            deleteModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to delete ticket.');
                        },
                        complete: function () {
                            button.prop('disabled', false).text('Delete');
                            deleteTicketId = null;
                        }
                    });
                });

                // Drag and drop
                $('.kanban-column').each(function () {
                    new Sortable(this, {
                        group: 'tickets',
                        animation: 150,
                        ghostClass: 'bg-light',
                        onEnd: function (evt) {
                            const ticketId  = $(evt.item).data('id');
                            const newStatus = $(evt.to).data('status');
                            const oldStatus = $(evt.from).data('status');

                            if (newStatus === oldStatus) return;

                            $.ajax({
                                url: `/tickets/${ticketId}/status`,
                                method: 'PATCH',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    status: newStatus,
                                },
                                success: function (response) {
                                    showSuccess(response.message);
                                    loadBoard();
                                },
                                error: function (xhr) {
                                    alert(xhr.responseJSON?.message || 'Unable to update ticket status.');
                                    loadBoard();
                                }
                            });
                        }
                    });
                });

                loadBoard();
            });
        </script>
    @endpush
</x-app-layout>

