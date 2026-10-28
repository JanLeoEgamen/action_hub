
<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Roles"
            action-label="Create"
            action-id="create-role"
        />
    </x-slot>

    <div class="container py-5">
        <div id="successMessage" class="alert alert-success d-none"></div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="create-role-modal" tabindex="-1"
        aria-labelledby="createRoleModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="create-role-form">
                    @csrf

                    <input type="hidden" id="create-role_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createRoleModalTitle">
                            Create Role
                        </h5>

                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="create-formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="create-name" class="form-label">
                                Role Name
                            </label>

                            <input type="text"
                                name="name"
                                id="create-name"
                                class="form-control"
                                required
                                minlength="4"
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Assign Permissions
                            </label>

                            <div class="border rounded p-3">
                            @php
                                $resourceTitles = [
                                    "companies" => "Companies",
                                    "departments" => "Departments",
                                    "groups" => "Groups",
                                    "users" => "Users",
                                    "roles" => "Roles",
                                    "task categories" => "Task Categories",
                                    "projects" => "Projects",
                                    "tasks" => "Tasks",
                                    "tickets" => "Tickets",
                                ];
                            @endphp

                            @if ($groupedPermissions->isNotEmpty())
                                @foreach ($groupedPermissions as $resource => $perms)
                                    <div class="card mb-3 border-light">
                                        <div class="card-header bg-light fw-semibold small text-uppercase py-2">
                                            {{ $resourceTitles[$resource] ?? ucfirst($resource) }}
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row">
                                                @foreach ($perms as $permission)
                                                    <div class="col-md-6 mb-2"
                                                         key="{{ $permission->id }}">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input permission-checkbox"
                                                                type="checkbox"
                                                                name="permission[]"
                                                                id="create-permission_{{ $permission->id }}"
                                                                value="{{ $permission->name }}">

                                                            <label
                                                                class="form-check-label"
                                                                for="create-permission_{{ $permission->id }}">
                                                                {{ ucfirst($permission->name) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">
                                    No permissions are available.
                                </p>
                            @endif
                        </div>

                            <small class="text-muted">
                                Select the permissions to assign to this role.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                            id="saveCreateRole"
                            class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-role-modal" tabindex="-1"
        aria-labelledby="editRoleModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="edit-role-form">
                    @csrf

                    <input type="hidden" id="edit-role_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoleModalTitle">
                            Edit Role
                        </h5>

                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="edit-formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">
                                Role Name
                            </label>

                            <input type="text"
                                name="name"
                                id="edit-name"
                                class="form-control"
                                required
                                minlength="4"
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Assign Permissions
                            </label>

                            <div class="border rounded p-3">
                            @php
                                $resourceTitles = [
                                    "companies" => "Companies",
                                    "departments" => "Departments",
                                    "groups" => "Groups",
                                    "users" => "Users",
                                    "roles" => "Roles",
                                    "task categories" => "Task Categories",
                                    "projects" => "Projects",
                                    "tasks" => "Tasks",
                                    "tickets" => "Tickets",
                                ];
                            @endphp

                            @if ($groupedPermissions->isNotEmpty())
                                @foreach ($groupedPermissions as $resource => $perms)
                                    <div class="card mb-3 border-light">
                                        <div class="card-header bg-light fw-semibold small text-uppercase py-2">
                                            {{ $resourceTitles[$resource] ?? ucfirst($resource) }}
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row">
                                                @foreach ($perms as $permission)
                                                    <div class="col-md-6 mb-2"
                                                         key="{{ $permission->id }}">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input permission-checkbox"
                                                                type="checkbox"
                                                                name="permission[]"
                                                                id="edit-permission_{{ $permission->id }}"
                                                                value="{{ $permission->name }}">

                                                            <label
                                                                class="form-check-label"
                                                                for="edit-permission_{{ $permission->id }}">
                                                                {{ ucfirst($permission->name) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">
                                    No permissions are available.
                                </p>
                            @endif
                        </div>

                            <small class="text-muted">
                                Select the permissions to assign to this role.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                            id="saveEditRole"
                            class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        id="deleteRoleModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this role?"
        confirm-button-id="confirmDeleteRole"
        confirm-label="Delete"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModalElement =
                    document.getElementById('create-role-modal');
                const createModal =
                    new bootstrap.Modal(createModalElement);

                const editModalElement =
                    document.getElementById('edit-role-modal');
                const editModal =
                    new bootstrap.Modal(editModalElement);

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,

                    ajax: {
                        url: '{{ route("roles.index") }}',
                        type: 'GET'
                    },

                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'permissions',
                            name: 'permissions',
                            orderable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                function resetCreateForm() {
                    $('#create-role-form')[0].reset();
                    $('#create-role_id').val('');
                    $('#create-role-form .permission-checkbox').prop('checked', false);
                    $('#create-formErrors').addClass('d-none').empty();
                }

                function resetEditForm() {
                    $('#edit-role-form')[0].reset();
                    $('#edit-role_id').val('');
                    $('#edit-role-form .permission-checkbox').prop('checked', false);
                    $('#edit-formErrors').addClass('d-none').empty();
                }

                function showSuccess(message) {
                    $('#successMessage')
                        .text(message)
                        .removeClass('d-none');

                    setTimeout(function () {
                        $('#successMessage').addClass('d-none');
                    }, 3000);
                }

                function showCreateErrors(xhr) {
                    const errorContainer = $('#create-formErrors');
                    errorContainer.empty();

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors)
                            .forEach(function (fieldErrors) {
                                fieldErrors.forEach(function (message) {
                                    $('<div>').text(message)
                                        .appendTo(errorContainer);
                                });
                            });

                        errorContainer.removeClass('d-none');
                    } else {
                        alert(
                            xhr.responseJSON?.message ||
                            'An unexpected error occurred.'
                        );
                    }
                }

                function showEditErrors(xhr) {
                    const errorContainer = $('#edit-formErrors');
                    errorContainer.empty();

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors)
                            .forEach(function (fieldErrors) {
                                fieldErrors.forEach(function (message) {
                                    $('<div>').text(message)
                                        .appendTo(errorContainer);
                                });
                            });

                        errorContainer.removeClass('d-none');
                    } else {
                        alert(
                            xhr.responseJSON?.message ||
                            'An unexpected error occurred.'
                        );
                    }
                }

                // Open modal for Create
                $('#create-role').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                // Open modal and load role data for Edit
                $('.datatable').on('click', '.edit-role', function (e) {
                    e.preventDefault();

                    const button = $(this);

                    if (button.data('loading')) {
                        return;
                    }

                    button.data('loading', true);
                    button.prop('disabled', true);

                    const originalText = button.text();
                    button.text('Loading...');

                    const id = button.data('id');

                    resetEditForm();

                    $.ajax({
                        url: `/roles/${id}/edit`,
                        method: 'GET',

                        success: function (role) {
                            $('#edit-role_id').val(role.id);
                            $('#edit-name').val(role.name);

                            $('#edit-role-form .permission-checkbox').prop('checked', false);

                            (role.permissions || []).forEach(function (name) {
                                $('#edit-role-form .permission-checkbox').filter(function () {
                                    return this.value === name;
                                }).prop('checked', true);
                            });

                            editModal.show();
                        },

                        error: function (xhr) {
                            alert(
                                xhr.responseJSON?.message ||
                                'Unable to load role.'
                            );
                        },

                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                // Create form submit
                $('#create-role-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateRole').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("roles.store") }}',
                        method: 'POST',
                        data: $(this).serialize(),

                        success: function (response) {
                            createModal.hide();
                            table.ajax.reload(null, false);
                            showSuccess(response.message);
                        },

                        error: function (xhr) {
                            showCreateErrors(xhr);
                        },

                        complete: function () {
                            $('#saveCreateRole').prop('disabled', false).text('Create');
                        }
                    });
                });

                // Edit form submit
                $('#edit-role-form').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#edit-role_id').val();

                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditRole').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/roles/${id}`,
                        method: 'PUT',
                        data: $(this).serialize(),

                        success: function (response) {
                            editModal.hide();
                            table.ajax.reload(null, false);
                            showSuccess(response.message);
                        },

                        error: function (xhr) {
                            showEditErrors(xhr);
                        },

                        complete: function () {
                            $('#saveEditRole').prop('disabled', false).text('Update');
                        }
                    });
                });
                // Delete confirmation modal
                const deleteModalElement =
                    document.getElementById('deleteRoleModal');

                const deleteModal =
                    new bootstrap.Modal(deleteModalElement);

                let deleteRoleId = null;

                // Open confirmation modal
                $('.datatable').on('click', '.delete-role', function (e) {
                    e.preventDefault();

                    if ($('#confirmDeleteRole').prop('disabled')) {
                        return;
                    }

                    deleteRoleId = $(this).data('id');

                    deleteModal.show();
                });

                // Confirm deletion
                $('#confirmDeleteRole').on('click', function () {
                    if (!deleteRoleId) {
                        return;
                    }

                    const button = $(this);

                    if (button.prop('disabled')) {
                        return;
                    }

                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/roles/${deleteRoleId}`,
                        method: 'DELETE',

                        data: {
                            _token: '{{ csrf_token() }}'
                        },

                        success: function (response) {
                            deleteModal.hide();

                            table.ajax.reload(null, false);

                            showSuccess(response.message);
                        },

                        error: function (xhr) {
                            alert(
                                xhr.responseJSON?.message ||
                                'Unable to delete role.'
                            );
                        },

                        complete: function () {
                            button.prop('disabled', false)
                                .text('Delete');

                            deleteRoleId = null;
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>







