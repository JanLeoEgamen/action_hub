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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1"
        aria-labelledby="projectModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="projectForm">
                    @csrf
                    <input type="hidden" id="project_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalTitle">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name"
                                class="form-control" required minlength="3" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description"
                                class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="status_form" class="form-label">Status</label>
                                <select name="status" id="status_form" class="form-select" required>
                                    <option value="planning">Planning</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Members</label>
                            <div id="member_ids" class="border rounded p-2"
                                style="max-height: 200px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="member_ids[]"
                                            value="{{ $user->id }}"
                                            id="member_{{ $user->id }}">
                                        <label class="form-check-label" for="member_{{ $user->id }}">
                                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="is_active" id="is_active_form" value="1" checked>
                            <label class="form-check-label" for="is_active_form">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveProject" class="btn btn-primary">Save</button>
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

    @push('scripts')
        <script>
            $(document).ready(function () {
                const projectModalElement = document.getElementById('projectModal');
                const projectModal = new bootstrap.Modal(projectModalElement);

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
                                            <li><strong>Owner:</strong> ${escapeHtml(p.owner || '—')}</li>
                                            <li><strong>Tickets:</strong> ${p.tickets}</li>
                                            <li><strong>Start:</strong> ${escapeHtml(p.start_date || '—')}</li>
                                            <li><strong>End:</strong> ${escapeHtml(p.end_date || '—')}</li>
                                        </ul>
                                        <div class="d-flex flex-wrap gap-1 mt-auto">
                                            ${p.action}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                let searchDebounce;
                $('#filter, #status, #is_active').on('change', loadList);
                $('#search').on('input', function () {
                    clearTimeout(searchDebounce);
                    searchDebounce = setTimeout(loadList, 300);
                });

                function resetForm() {
                    $('#projectForm')[0].reset();
                    $('#project_id').val('');
                    $('#member_ids input[type="checkbox"]').prop('checked', false);
                    $('#formErrors').addClass('d-none').empty();
                }

                $('#create-project').on('click', function () {
                    resetForm();
                    $('#projectModalTitle').text('Create Project');
                    $('#saveProject').text('Create');
                    projectModal.show();
                });

                $('#projectCardGrid').on('click', '.edit-project', function (e) {
                    e.preventDefault();

                    const button = $(this);
                    if (button.data('loading')) return;
                    button.data('loading', true).prop('disabled', true);
                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');
                    resetForm();

                    $.ajax({
                        url: `/projects/${id}/data`,
                        method: 'GET',
                        success: function (project) {
                            $('#project_id').val(project.id);
                            $('#name').val(project.name);
                            $('#description').val(project.description);
                            $('#status_form').val(project.status);
                            $('#start_date').val(project.start_date);
                            $('#end_date').val(project.end_date);
                            $('#is_active_form').prop('checked', !!project.is_active);
                            $('#member_ids input[type="checkbox"]').prop('checked', false);
                            (project.member_ids || []).forEach(function (uid) {
                                $('#member_ids input[type="checkbox"][value="' + uid + '"]')
                                    .prop('checked', true);
                            });
                            $('#projectModalTitle').text('Edit Project');
                            $('#saveProject').text('Update');
                            projectModal.show();
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

                $('#projectForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#project_id').val();
                    const url = id ? `/projects/${id}` : '{{ route("projects.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveProject').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function (response) {
                            projectModal.hide();
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
                                $('#formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save project.');
                            }
                        },
                        complete: function () {
                            $('#saveProject')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
                        }
                    });
                });

                // Delete
                const deleteModalElement = document.getElementById('deleteProjectModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);
                let deleteProjectId = null;

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