<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Tasks"
            action-label="Create"
            action-id="create-task"
            permission="create tasks"
        />
    </x-slot>

    <div class="container py-5">
        <div id="successMessage" class="alert alert-success d-none"></div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">View</label>
                        <select id="filter" class="form-select">
                            <option value="all">All Tasks</option>
                            <option value="assigned_by_me">Tasks I Assigned</option>
                            <option value="assigned_to_me">Tasks Assigned to Me</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" id="search" class="form-control" placeholder="Search by title...">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select id="category_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="col-md-2">
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
                'pending'     => 'Pending',
                'in_progress' => 'In Progress',
                'completed'   => 'Completed',
            ] as $key => $label)
                <div class="col-md-4">
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
    <div class="modal fade" id="taskModal" tabindex="-1"
        aria-labelledby="taskModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="taskForm">
                    @csrf
                    <input type="hidden" id="task_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalTitle">Create Task</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="col-md-6 mb-3">
                                <label for="category_id_form" class="form-label">Category</label>
                                <select name="category_id" id="category_id_form"
                                    class="form-select" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Assignees</label>
                            <div id="assignee_ids" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input assignee-checkbox"
                                            type="checkbox"
                                            name="assignee_ids[]"
                                            value="{{ $user->id }}"
                                            id="assignee_{{ $user->id }}">
                                        <label class="form-check-label" for="assignee_{{ $user->id }}">
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status_form" class="form-label">Status</label>
                                <select name="status" id="status_form"
                                    class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
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
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveTask"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        id="deleteTaskModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this task?"
        confirm-button-id="confirmDeleteTask"
        confirm-label="Delete"
    />

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            $(document).ready(function () {
                const taskModalElement = document.getElementById('taskModal');
                const taskModal = new bootstrap.Modal(taskModalElement);

                const STATUS_LABELS = {
                    pending: 'Pending',
                    in_progress: 'In Progress',
                    completed: 'Completed',
                };

                // helpers
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

                function isOverdue(task) {
                    if (!task.due_date) return false;
                    if (task.status === 'completed') return false;
                    return task.due_date < todayISO();
                }

                // loading board
                function loadBoard() {
                    const params = {
                        filter:      $('#filter').val(),
                        search:      $('#search').val(),
                        category_id: $('#category_id').val(),
                        status:      $('#status').val(),
                        overdue:     $('#overdue').is(':checked') ? 1 : 0,
                    };

                    $.ajax({
                        url: '{{ route("tasks.board") }}',
                        method: 'GET',
                        data: params,
                        success: function (response) {
                            renderBoard(response.tasks || []);
                        },
                        error: function (xhr) {
                            console.error('Board load failed', xhr.status, xhr.responseText);
                            alert('Unable to load tasks.');
                        }
                    });
                }

                // render the board with tasks
                function renderBoard(tasks) {
                    const groups = {
                        pending: [],
                        in_progress: [],
                        completed: [],
                    };

                    tasks.forEach(function (task) {
                        if (groups[task.status]) {
                            groups[task.status].push(task);
                        }
                    });

                    Object.keys(groups).forEach(function (status) {
                        const $col = $('.kanban-column[data-status="' + status + '"]');
                        $col.empty();
                        $('[data-count="' + status + '"]').text(groups[status].length);

                        groups[status].forEach(function (task) {
                            $col.append(buildCard(task));
                        });
                    });
                }

                // cards
                function buildCard(task) {
                    const assignees = task.assignees && task.assignees.length
                        ? task.assignees.map(function (a) { return escapeHtml(a.name); }).join(', ')
                        : '—';

                    const dueClass = isOverdue(task) ? 'text-danger fw-bold' : 'text-muted';
                    const dueText  = task.due_date ? task.due_date : 'No due date';

                    return `
                        <div class="card mb-2 task-card" data-id="${task.id}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">${escapeHtml(task.title)}</h6>
                                    <span class="badge bg-secondary">${escapeHtml(STATUS_LABELS[task.status] || task.status)}</span>
                                </div>
                                <div class="small text-muted mb-1">${escapeHtml(task.category || '—')}</div>
                                <div class="small mb-1"><strong>Created by:</strong> ${escapeHtml(task.creator || '—')}</div>
                                <div class="small mb-1"><strong>Assignees:</strong> ${assignees}</div>
                                <div class="small mb-2 ${dueClass}"><strong>Due:</strong> ${escapeHtml(dueText)}</div>
                                <div class="text-end">${task.action}</div>
                            </div>
                        </div>
                    `;
                }

                // filters
                let searchDebounce;
                $('#filter, #category_id, #status, #overdue').on('change', loadBoard);
                $('#search').on('input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadBoard, 300);
                });

                // reset and clear form
                function resetForm() {
                    $('#taskForm')[0].reset();
                    $('#task_id').val('');
                    $('#assignee_ids input[type="checkbox"]').prop('checked', false);
                    $('#formErrors').addClass('d-none').empty();
                }

                $('#create-task').on('click', function () {
                    resetForm();
                    $('#taskModalTitle').text('Create Task');
                    $('#saveTask').text('Create');
                    taskModal.show();
                });

                $('#kanban-board').on('click', '.edit-task', function (e) {
                    e.preventDefault();

                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);

                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetForm();

                    $.ajax({
                        url: `/tasks/${id}/data`,
                        method: 'GET',
                        success: function (task) {
                            $('#task_id').val(task.id);
                            $('#title').val(task.title);
                            $('#description').val(task.description);
                            $('#category_id_form').val(task.category_id);
                            $('#due_date').val(task.due_date);
                            $('#status_form').val(task.status);
                            $('#is_active').prop('checked', !!task.is_active);
                            $('#assignee_ids input[type="checkbox"]').prop('checked', false);
                            (task.assignee_ids || []).forEach(function (uid) {
                                $('#assignee_ids input[type="checkbox"][value="' + uid + '"]').prop('checked', true);
                            });
                            $('#taskModalTitle').text('Edit Task');
                            $('#saveTask').text('Update');
                            taskModal.show();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to load task.');
                        },
                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                $('#taskForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#task_id').val();
                    const url = id ? `/tasks/${id}` : '{{ route("tasks.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveTask').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function (response) {
                            taskModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let messages = '';
                                Object.values(errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages += `<div>${message}</div>`;
                                    });
                                });
                                $('#formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save task.');
                            }
                        },
                        complete: function () {
                            $('#saveTask')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
                        }
                    });
                });

                // Delete
                const deleteModalElement = document.getElementById('deleteTaskModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);

                let deleteTaskId = null;

                $('#kanban-board').on('click', '.delete-task', function (e) {
                    e.preventDefault();
                    if ($('#confirmDeleteTask').prop('disabled')) return;
                    deleteTaskId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteTask').on('click', function () {
                    if (! deleteTaskId) return;

                    const button = $(this);
                    if (button.prop('disabled')) return;
                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/tasks/${deleteTaskId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            deleteModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to delete task.');
                        },
                        complete: function () {
                            button.prop('disabled', false).text('Delete');
                            deleteTaskId = null;
                        }
                    });
                });

                // Drag and drop
                $('.kanban-column').each(function () {
                    new Sortable(this, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'bg-light',
                        onEnd: function (evt) {
                            const taskId    = $(evt.item).data('id');
                            const newStatus = $(evt.to).data('status');
                            const oldStatus = $(evt.from).data('status');

                            if (newStatus === oldStatus) return;

                            $.ajax({
                                url: `/tasks/${taskId}/status`,
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
                                    alert(xhr.responseJSON?.message || 'Unable to update task status.');
                                    loadBoard();
                                }
                            });
                        }
                    });
                });

                // load the board initially
                loadBoard();
            });
        </script>
    @endpush
</x-app-layout>