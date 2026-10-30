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

    <!-- Create Modal -->
    <div class="modal fade" id="create-task-modal" tabindex="-1"
        aria-labelledby="createTaskModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-task-form">
                    @csrf
                    <input type="hidden" id="create-task_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createTaskModalTitle">Create Task</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Category</label>
                                <div id="create-category_ids" class="border rounded p-2"
                                    style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input category-radio"
                                                type="radio"
                                                name="category_id"
                                                value="{{ $category->id }}"
                                                id="create-category_{{ $category->id }}">
                                            <label class="form-check-label"
                                                for="create-category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create-due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="create-due_date" class="form-control">
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
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create-status_form" class="form-label">Status</label>
                                <select name="status" id="create-status_form"
                                    class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create-is_active" class="form-label">Status</label>
                                <select name="is_active" id="create-is_active" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCreateTask"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-task-modal" tabindex="-1"
        aria-labelledby="editTaskModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit-task-form">
                    @csrf
                    <input type="hidden" id="edit-task_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalTitle">Edit Task</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Category</label>
                                <div id="edit-category_ids" class="border rounded p-2"
                                    style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input category-radio"
                                                type="radio"
                                                name="category_id"
                                                value="{{ $category->id }}"
                                                id="edit-category_{{ $category->id }}">
                                            <label class="form-check-label"
                                                for="edit-category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="edit-due_date" class="form-control">
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
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-status_form" class="form-label">Status</label>
                                <select name="status" id="edit-status_form"
                                    class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-is_active" class="form-label">Status</label>
                                <select name="is_active" id="edit-is_active" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveEditTask"
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

    <!-- Edit Confirmation Modal -->
    <x-edit-confirmation-modal
        id="editConfirmTaskModal"
        title="Confirm Edit"
        message="Are you sure you want to edit this task?"
        confirm-button-id="confirmEditTask"
        confirm-label="Edit"
    />

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            $(document).ready(function () {
                const createModal = new bootstrap.Modal(document.getElementById('create-task-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-task-modal'));
                const editConfirmModal = new bootstrap.Modal(document.getElementById('editConfirmTaskModal'));

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
                        if (groups.hasOwnProperty(task.status)) {
                            groups[task.status].push(task);
                        }
                    });

                    Object.keys(groups).forEach(function (status) {
                        const $col = $(`.kanban-column[data-status="${status}"]`);
                        $col.empty();

                        const countEl = $col.closest('.card').find('.badge[data-count]');
                        if (countEl.length) {
                            countEl.text(groups[status].length);
                        }

                        groups[status].forEach(function (task) {
                            const $card = $(`
                                <div class="card mb-2 task-card" data-id="${task.id}">
                                    <div class="card-body">
                                        <h6 class="card-title">${escapeHtml(task.title)}</h6>
                                        <p class="card-text text-muted small">${escapeHtml(task.description || '')}</p>
                                        ${task.due_date ? `<small class="text-muted">Due: ${escapeHtml(task.due_date)}</small><br>` : ''}
                                        ${task.creator ? `<small class="text-muted">Created by: ${escapeHtml(task.creator)}</small><br>` : ''}
                                        <button type="button" class="btn btn-sm btn-success edit-task" data-id="${task.id}">Edit</button>
                                        <button type="button" class="btn btn-sm btn-danger delete-task" data-id="${task.id}">Delete</button>
                                    </div>
                                </div>
                            `);
                            $col.append($card);
                        });
                    });
                }

                function resetCreateForm() {
                    $('#create-task-form')[0].reset();
                    $('#create-task_id').val('');
                    $('#create-assignee_ids input[type="checkbox"]').prop('checked', false);
                    $('#create-formErrors').addClass('d-none').empty();
                }

                function resetEditForm() {
                    $('#edit-task-form')[0].reset();
                    $('#edit-task_id').val('');
                    $('#edit-assignee_ids input[type="checkbox"]').prop('checked', false);
                    $('#edit-formErrors').addClass('d-none').empty();
                }

                $('#create-task').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                // Open Edit Confirmation modal for Edit
                $('#kanban-board').on('click', '.edit-task', function (e) {
                    e.preventDefault();

                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);

                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetEditForm();

                    $.ajax({
                        url: `/tasks/${id}/edit`,
                        method: 'GET',
                        success: function (task) {
                            $('#edit-task_id').val(task.id);
                            $('#edit-title').val(task.title);
                            $('#edit-description').val(task.description);
                            $('#edit-category_ids input[type="radio"]').prop('checked', false);
                            $('#edit-category_ids input[type="radio"][value="' + task.category_id + '"]').prop('checked', true);
                            $('#edit-due_date').val(task.due_date);
                            $('#edit-status_form').val(task.status);
                            $('#edit-is_active').val(task.is_active ? '1' : '0');
                            $('#edit-assignee_ids input[type="checkbox"]').prop('checked', false);
                            (task.assignee_ids || []).forEach(function (uid) {
                                $('#edit-assignee_ids input[type="checkbox"][value="' + uid + '"]').prop('checked', true);
                            });
                            editModal.show();
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

                // Confirm Edit before saving
                $('#edit-task-form').on('submit', function (e) {
                    e.preventDefault();
                    editConfirmTaskId = $('#edit-task_id').val();
                    editConfirmModal.show();
                });

                $('#confirmEditTask').on('click', function () {
                    if (!editConfirmTaskId) return;
                    editConfirmModal.hide();

                    const button = $(this).prop('disabled', true).text('Loading...');
                    const originalText = button.text();
                    button.data('loading', true);
                    button.prop('disabled', true);

                    const id = $('#edit-task_id').val();

                    $.ajax({
                        url: `/tasks/${id}`,
                        method: 'PUT',
                        data: $('#edit-task-form').serialize(),
                        success: function (response) {
                            editModal.hide();
                            showSuccess(response.message);
                            loadBoard();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to save task.');
                        },
                        complete: function () {
                            button.prop('disabled', false).text(originalText);
                            button.removeData('loading');
                            editConfirmTaskId = null;
                        }
                    });
                });

                $('#create-task-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateTask').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("tasks.store") }}',
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
                                alert(xhr.responseJSON?.message || 'Unable to save task.');
                            }
                        },
                        complete: function () {
                            $('#saveCreateTask').prop('disabled', false).text('Save');
                        }
                    });
                });

                $('#edit-task-form').on('submit', function (e) {
                    e.preventDefault();

                    editConfirmTaskId = $('#edit-task_id').val();
                    editConfirmModal.show();
                });

                // Delete
                const deleteModalElement = document.getElementById('deleteTaskModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);
                let deleteTaskId = null;
                let editConfirmTaskId = null;

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

                // Filter change handlers
                $('#filter, #category_id, #status').on('change', loadBoard);
                let searchDebounce;
                $('#search').on('input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadBoard, 300);
                });
                $('#overdue').on('change', loadBoard);

                loadBoard();
            });
        </script>
    @endpush</x-app-layout>
