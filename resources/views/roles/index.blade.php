
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1"
        aria-labelledby="roleModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="roleForm">
                    @csrf

                    <input type="hidden" id="role_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalTitle">
                            Create Role
                        </h5>

                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Role Name
                            </label>

                            <input type="text"
                                name="name"
                                id="name"
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
                                @if ($permissions->isNotEmpty())
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-6 mb-2"
                                                key="{{ $permission->id }}">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input permission-checkbox"
                                                        type="checkbox"
                                                        name="permission[]"
                                                        id="permission_{{ $permission->id }}"
                                                        value="{{ $permission->name }}">

                                                    <label
                                                        class="form-check-label"
                                                        for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
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
                            id="saveRole"
                            class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
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
                const roleModalElement =
                    document.getElementById('roleModal');

                const roleModal =
                    new bootstrap.Modal(roleModalElement);

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

                function resetForm() {
                    $('#roleForm')[0].reset();
                    $('#role_id').val('');

                    $('.permission-checkbox').prop('checked', false);

                    $('#formErrors')
                        .addClass('d-none')
                        .empty();
                }

                function showSuccess(message) {
                    $('#successMessage')
                        .text(message)
                        .removeClass('d-none');

                    setTimeout(function () {
                        $('#successMessage').addClass('d-none');
                    }, 3000);
                }

                function showErrors(xhr) {
                    const errorContainer = $('#formErrors');
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
                    resetForm();

                    $('#roleModalTitle').text('Create Role');
                    $('#saveRole').text('Create');

                    roleModal.show();
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

                    resetForm();

                    $.ajax({
                        url: `/roles/${id}/data`,
                        method: 'GET',

                        success: function (role) {
                            $('#role_id').val(role.id);
                            $('#name').val(role.name);

                            $('.permission-checkbox').prop('checked', false);

                            (role.permissions || []).forEach(function (name) {
                                $('.permission-checkbox').filter(function () {
                                    return this.value === name;
                                }).prop('checked', true);
                            });

                            $('#roleModalTitle').text('Edit Role');
                            $('#saveRole').text('Update');

                            roleModal.show();
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

                // Create or Update
                $('#roleForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#role_id').val();

                    const url = id
                        ? `/roles/${id}`
                        : '{{ route("roles.store") }}';

                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors')
                        .addClass('d-none')
                        .empty();

                    $('#saveRole')
                        .prop('disabled', true)
                        .text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),

                        success: function (response) {
                            roleModal.hide();

                            table.ajax.reload(null, false);

                            showSuccess(response.message);
                        },

                        error: function (xhr) {
                            showErrors(xhr);
                        },

                        complete: function () {
                            $('#saveRole')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
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
