<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="{{ $project->name }} — Tickets"
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1"
        aria-labelledby="ticketModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="ticketForm">
                    @csrf
                    <input type="hidden" id="ticket_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalTitle">Create Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="label_form" class="form-label">Label</label>
                                <select name="label" id="label_form" class="form-select" required>
                                    <option value="task">Task</option>
                                    <option value="bug">Bug</option>
                                    <option value="feature">Feature</option>
                                    <option value="improvement">Improvement</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="priority_form" class="form-label">Priority</label>
                                <select name="priority" id="priority_form" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status_form" class="form-label">Status</label>
                                <select name="status" id="status_form" class="form-select" required>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveTicket" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
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

                const ticketModalElement = document.getElementById('ticketModal');
                const ticketModal = new bootstrap.Modal(ticketModalElement);

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

                function todayISO() {
                    const d = new Date();
                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${y}-${m}-${day}`;
                }

                function isOverdue(ticket) {
                    if (! ticket.due_date) return false;
                    if (ticket.status === 'closed') return false;
                    return ticket.due_date < todayISO();
                }

                function loadBoard() {
                    const params = {
                        search:   $('#search').val(),
                        label:    $('#label').val(),
                        priority: $('#priority').val(),
                        status:   $('#status').val(),
                        overdue:  $('#overdue').is(':checked') ? 1 : 0,
                    };

                    $.ajax({
                        url: `/projects/${PROJECT_ID}/tickets/board`,
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

                function renderBoard(tickets) {
                    const groups = { open: [], in_progress: [], resolved: [], closed: [] };

                    tickets.forEach(function (ticket) {
                        if (groups[ticket.status]) groups[ticket.status].push(ticket);
                    });

                    Object.keys(groups).forEach(function (status) {
                        const $col = $('.kanban-column[data-status="' + status + '"]');
                        $col.empty();
                        $('[data-count="' + status + '"]').text(groups[status].length);

                        groups[status].forEach(function (ticket) {
                            $col.append(buildCard(ticket));
                        });
                    });
                }

                function buildCard(ticket) {
                    const dueClass = isOverdue(ticket) ? 'text-danger fw-bold' : 'text-muted';
                    const dueText  = ticket.due_date ? ticket.due_date : 'No due date';
                    const priorityColor = {
                        low: 'bg-secondary',
                        medium: 'bg-info',
                        high: 'bg-warning',
                        urgent: 'bg-danger',
                    }[ticket.priority] || 'bg-secondary';

                    return `
                        <div class="card mb-2 ticket-card" data-id="${ticket.id}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">${escapeHtml(ticket.title)}</h6>
                                    <span class="badge ${priorityColor}">${escapeHtml(ticket.priority)}</span>
                                </div>
                                <div class="small text-muted mb-1">${escapeHtml(ticket.label || '—')}</div>
                                <div class="small mb-1 ${dueClass}"><strong>Due:</strong> ${escapeHtml(dueText)}</div>
                                <div class="text-end">${ticket.action}</div>
                            </div>
                        </div>
                    `;
                }

                let searchDebounce;
                $('#label, #priority, #status, #overdue').on('change', loadBoard);
                $('#search').on('input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadBoard, 300);
                });

                function resetForm() {
                    $('#ticketForm')[0].reset();
                    $('#ticket_id').val('');
                    $('#formErrors').addClass('d-none').empty();
                }

                $('#create-ticket').on('click', function () {
                    resetForm();
                    $('#ticketModalTitle').text('Create Ticket');
                    $('#saveTicket').text('Create');
                    ticketModal.show();
                });

                $('#kanban-board').on('click', '.edit-ticket', function (e) {
                    e.preventDefault();
                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);
                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetForm();

                    $.ajax({
                        url: `/tickets/${id}/data`,
                        method: 'GET',
                        success: function (ticket) {
                            $('#ticket_id').val(ticket.id);
                            $('#title').val(ticket.title);
                            $('#description').val(ticket.description);
                            $('#label_form').val(ticket.label);
                            $('#priority_form').val(ticket.priority);
                            $('#status_form').val(ticket.status);
                            $('#due_date').val(ticket.due_date);
                            $('#is_active').prop('checked', !!ticket.is_active);
                            $('#ticketModalTitle').text('Edit Ticket');
                            $('#saveTicket').text('Update');
                            ticketModal.show();
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

                $('#ticketForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#ticket_id').val();
                    const url = id
                        ? `/tickets/${id}`
                        : `/projects/${PROJECT_ID}/tickets`;
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveTicket').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function (response) {
                            ticketModal.hide();
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
                                $('#formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save ticket.');
                            }
                        },
                        complete: function () {
                            $('#saveTicket')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
                        }
                    });
                });

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