<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Projects"
            action-label="Create"
            action-id="create-project"
            permission="create projects"
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
                            <option value="all">All Projects</option>
                            <option value="owned_by_me">Projects I Own</option>
                            <option value="member_of">Projects I'm In</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" id="search" class="form-control" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="status" class="form-select">
                            <option value="">All</option>
                            <option value="planning">Planning</option>
                            <option value="in_progress">In Progress</option>
                            <option value="on_hold">On Hold</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Active</label>
                        <select id="is_active" class="form-select">
                            <option value="all">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Project list --}}
        <div class="row g-3" id="projectCardGrid">
            <div class="col-12 text-center text-muted py-4">Loading...</div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="create-project-modal" tabindex="-1"
        aria-labelledby="createProjectModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-project-form">
                    @csrf
                    <input type="hidden" id="create-project_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalTitle">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="create-name" class="form-label">Name</label>
                            <input type="text" name="name" id="create-name"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="create-description" class="form-label">Description</label>
                            <textarea name="description" id="create-description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="create-status_form" class="form-label">Status</label>
                                <select name="status" id="create-status_form" class="form-select" required>
                                    <option value="planning">Planning</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="create-start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="create-start_date" class="form-control" min="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="create-end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="create-end_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Members</label>
                            <div id="create-member_ids" class="border rounded p-2"
                                style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="member_ids[]"
                                            value="{{ $user->id }}"
                                            id="create-member_{{ $user->id }}">
                                        <label class="form-check-label" for="create-member_{{ $user->id }}">
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="is_active" id="create-is_active_form" value="1" checked>
                            <label class="form-check-label" for="create-is_active_form">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCreateProject" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-project-modal" tabindex="-1"
        aria-labelledby="editProjectModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit-project-form">
                    @csrf
                    <input type="hidden" id="edit-project_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editProjectModalTitle">Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name</label>
                            <input type="text" name="name" id="edit-name"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Description</label>
                            <textarea name="description" id="edit-description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit-status_form" class="form-label">Status</label>
                                <select name="status" id="edit-status_form" class="form-select" required>
                                    <option value="planning">Planning</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit-start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="edit-start_date" class="form-control" min="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit-end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="edit-end_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Members</label>
                            <div id="edit-member_ids" class="border rounded p-2"
                                style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="member_ids[]"
                                            value="{{ $user->id }}"
                                            id="edit-member_{{ $user->id }}">
                                        <label class="form-check-label" for="edit-member_{{ $user->id }}">
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="is_active" id="edit-is_active_form" value="1" checked>
                            <label class="form-check-label" for="edit-is_active_form">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveEditProject" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        id="deleteProjectModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this project?"
        confirm-button-id="confirmDeleteProject"
        confirm-label="Delete"
    />

    <!-- Edit Confirmation Modal -->
    <x-edit-confirmation-modal
        id="editConfirmProjectModal"
        title="Confirm Edit"
        message="Are you sure you want to edit this project?"
        confirm-button-id="confirmEditProject"
        confirm-label="Save Project"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModal = new bootstrap.Modal(document.getElementById('create-project-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-project-modal'));
                const editConfirmModal = new bootstrap.Modal(document.getElementById('editConfirmProjectModal'));

                const STATUS_LABELS = {
                    planning: 'Planning',
                    in_progress: 'In Progress',
                    on_hold: 'On Hold',
                    completed: 'Completed',
                    cancelled: 'Cancelled',
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

                function formatDate(isoDate) {
                    if (!isoDate) return '';
                    const parts = isoDate.split('-');
                    if (parts.length !== 3) return isoDate;
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10);
                    const d = parseInt(parts[2], 10);
                    const date = new Date(y, m - 1, d);
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
                }

                function loadList() {
                    const params = {
                        filter:    $('#filter').val(),
                        search:    $('#search').val(),
                        status:    $('#status').val(),
                        is_active: $('#is_active').val(),
                    };

                    $.ajax({
                        url: '{{ route("projects.list") }}',
                        method: 'GET',
                        data: params,
                        success: function (response) {
                            renderList(response.projects || []);
                        },
                        error: function (xhr) {
                            console.error('List load failed', xhr.status, xhr.responseText);
                            alert('Unable to load projects.');
                        }
                    });
                }

                function renderList(projects) {
                    const $grid = $('#projectCardGrid');
                    $grid.empty();

                    if (! projects.length) {
                        $grid.append(
                            '<div class="col-12 text-center text-muted py-4">No projects found.</div>'
                        );
                        return;
                    }

                    projects.forEach(function (p) {
                        const statusBadge = `<span class="badge bg-secondary">${escapeHtml(STATUS_LABELS[p.status] || p.status)}</span>`;
                        const activeBadge = p.is_active
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>';

                        $grid.append(`
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">${escapeHtml(p.name)}</h5>
                                            ${activeBadge}
                                        </div>
                                        <div class="mb-2">${statusBadge}</div>
                                        <p class="card-text text-muted small flex-grow-1">
                                            ${escapeHtml(p.description || 'No description.')}
                                        </p>
                                        <ul class="list-unstyled small mb-3">
                                            <li><strong>Owner:</strong> ${escapeHtml(p.owner || '\u2014')}</li>
                                            <li><strong>Tickets:</strong> ${p.tickets}</li>
                                            <li><strong>Start:</strong> ${escapeHtml(formatDate(p.start_date) || '\u2014')}</li>
                                            <li><strong>End:</strong> ${escapeHtml(formatDate(p.end_date) || '\u2014')}</li>
                                        </ul>
                                        <div class="mt-auto d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-success edit-project" data-id="${p.id}">Edit</button>
                                            <button type="button" class="btn btn-sm btn-danger delete-project" data-id="${p.id}">Delete</button>
                                            ${p.action}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                function resetCreateForm() {
                    $('#create-project-form')[0].reset();
                    $('#create-project_id').val('');
                    $('#create-member_ids input[type="checkbox"]').prop('checked', false);
                    $('#create-is_active_form').prop('checked', true);
                    $('#create-formErrors').addClass('d-none').empty();
                }

                function resetEditForm() {
                    $('#edit-project-form')[0].reset();
                    $('#edit-project_id').val('');
                    $('#edit-member_ids input[type="checkbox"]').prop('checked', false);
                    $('#edit-formErrors').addClass('d-none').empty();
                }

                $('#create-project').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                // Open Edit Confirmation modal for Edit.
                $('#projectCardGrid').on('click', '.edit-project', function (e) {
                    e.preventDefault();

                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);
                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetEditForm();

                    $.ajax({
                        url: `/projects/${id}/edit`,
                        method: 'GET',
                        success: function (project) {
                            $('#edit-project_id').val(project.id);
                            $('#edit-name').val(project.name);
                            $('#edit-description').val(project.description);
                            $('#edit-status_form').val(project.status);
                            $('#edit-start_date').val(project.start_date);
                            originalEditStartDate = project.start_date;
                            $('#edit-end_date').val(project.end_date);
                            $('#edit-is_active_form').prop('checked', !!project.is_active);
                            $('#edit-member_ids input[type="checkbox"]').prop('checked', false);
                            (project.member_ids || []).forEach(function (uid) {
                                $('#edit-member_ids input[type="checkbox"][value="' + uid + '"]')
                                    .prop('checked', true);
                            });
                            editModal.show();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to load project.');
                        },
                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                // Confirm Edit before saving
                $('#edit-project-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#edit-formErrors').addClass('d-none').empty();

                    const editStartDate = $('#edit-start_date').val();
                    const today = new Date().toISOString().split('T')[0];
                    if (editStartDate && editStartDate !== originalEditStartDate && editStartDate < today) {
                        $('#edit-formErrors').html('<div>The start date cannot be earlier than today.</div>').removeClass('d-none');
                        return;
                    }

                    const editEndDate = $('#edit-end_date').val();
                    if (editStartDate && editEndDate && editEndDate < editStartDate) {
                        $('#edit-formErrors').html('<div>The end date cannot be earlier than the start date.</div>').removeClass('d-none');
                        return;
                    }

                    editConfirmProjectId = $('#edit-project_id').val();
                    editConfirmModal.show();
                });

                $('#confirmEditProject').on('click', function () {
                    if (!editConfirmProjectId) return;
                    editConfirmModal.hide();

                    const button = $(this).prop('disabled', true).text('Loading...');
                    const originalText = button.text();
                    button.data('loading', true);
                    button.prop('disabled', true);

                    const id = $('#edit-project_id').val();

                    $.ajax({
                        url: `/projects/${id}`,
                        method: 'PUT',
                        data: $('#edit-project-form').serialize(),
                        success: function (response) {
                            editModal.hide();
                            showSuccess(response.message);
                            loadList();
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
                                alert(xhr.responseJSON?.message || 'Unable to save project.');
                            }
                        },
                        complete: function () {
                            button.prop('disabled', false).text(originalText);
                            button.removeData('loading');
                            editConfirmProjectId = null;
                        }
                    });
                });

                $('#create-project-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();

                    const createStartDate = $('#create-start_date').val();
                    const today = new Date().toISOString().split('T')[0];
                    if (createStartDate && createStartDate < today) {
                        $('#create-formErrors').html('<div>The start date cannot be earlier than today.</div>').removeClass('d-none');
                        $('#saveCreateProject').prop('disabled', false).text('Save');
                        return;
                    }

                    const createEndDate = $('#create-end_date').val();
                    if (createStartDate && createEndDate && createEndDate < createStartDate) {
                        $('#create-formErrors').html('<div>The end date cannot be earlier than the start date.</div>').removeClass('d-none');
                        $('#saveCreateProject').prop('disabled', false).text('Save');
                        return;
                    }

                    $('#saveCreateProject').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("projects.store") }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            createModal.hide();
                            showSuccess(response.message);
                            if (response.project && response.project.id) {
                                window.location.href = "{{ route('projects.tickets', ['projectId' => '__ID__']) }}".replace('__ID__', response.project.id);
                            } else {
                                loadList();
                            }
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
                                alert(xhr.responseJSON?.message || 'Unable to save project.');
                            }
                        },
                        complete: function () {
                            $('#saveCreateProject').prop('disabled', false).text('Save');
                        }
                    });
                });

                // Filters
                $('#filter, #status, #is_active').on('change', loadList);
                let searchDebounce;
                $('#search').on('input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadList, 300);
                });

                // Delete
                const deleteModalElement = document.getElementById('deleteProjectModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);
                let deleteProjectId = null;
                let editConfirmProjectId = null;
                let originalEditStartDate = null;

                $('#projectCardGrid').on('click', '.delete-project', function (e) {
                    e.preventDefault();
                    if ($('#confirmDeleteProject').prop('disabled')) return;
                    deleteProjectId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteProject').on('click', function () {
                    if (! deleteProjectId) return;

                    const button = $(this);
                    if (button.prop('disabled')) return;
                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/projects/${deleteProjectId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            deleteModal.hide();
                            showSuccess(response.message);
                            loadList();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to delete project.');
                        },
                        complete: function () {
                            button.prop('disabled', false).text('Delete');
                            deleteProjectId = null;
                        }
                    });
                });

                loadList();
            });
        </script>
    @endpush
</x-app-layout>

