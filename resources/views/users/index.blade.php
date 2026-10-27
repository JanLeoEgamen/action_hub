<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Users"
            action-label="Create"
            action-id="create-user"
            permission="create users"
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
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Group</th>
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
    <div class="modal fade" id="userModal" tabindex="-1"
        aria-labelledby="modalTitle" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <form id="userForm">
                    @csrf

                    <input type="hidden" id="user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">
                            Create User
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">
                                First Name
                            </label>

                            <input type="text"
                                name="first_name"
                                id="first_name"
                                class="form-control"
                                required
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">
                                Last Name
                            </label>

                            <input type="text"
                                name="last_name"
                                id="last_name"
                                class="form-control"
                                required
                                minlength="4"
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                required
                                maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="group_id" class="form-label">Group</label>
                            <select name="group_id" id="group_id" class="form-select">
                                <option value="">Select Group (Optional)</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->company_name }} - {{ $group->department_name }} - {{ $group->group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Roles</label>

                            <div id="roleCheckboxes" class="border rounded p-3">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            id="role_{{ $role->id }}"
                                            value="{{ $role->id }}"
                                            class="form-check-input role-checkbox"
                                        >

                                        <label
                                            for="role_{{ $role->id }}"
                                            class="form-check-label"
                                        >
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                required
                                minlength="8"
                                autocomplete="new-password">


                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                Confirm Password
                            </label>

                            <input type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="form-control"
                                required
                                minlength="8"
                                autocomplete="new-password">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                            id="saveUser"
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
        id="deleteUserModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this user?"
        confirm-button-id="confirmDeleteUser"
        confirm-label="Delete"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const modalElement = document.getElementById('userModal');
                const modal = new bootstrap.Modal(modalElement);

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,

                    ajax: {
                        url: '{{ route("users.index") }}',
                        type: 'GET'
                    },

                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        { data: 'first_name', name: 'first_name' },
                        { data: 'last_name', name: 'last_name' },
                        { data: 'email', name: 'email' },
                        { data: 'roles', name: 'roles' },
                        { data: 'group_name', name: 'group_name' },
                        { data: 'created_at', name: 'created_at' },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                function resetForm() {
                    $('#userForm')[0].reset();

                    $('#user_id').val('');

                    $('#formErrors')
                        .addClass('d-none')
                        .empty();

                    $('#password, #confirm_password')
                        .prop('required', true);
                }

                // Open modal for Create.
                $('#create-user').on('click', function () {
                    resetForm();

                    $('#modalTitle').text('Create User');
                    $('#saveUser').text('Create');

                    modal.show();
                });

                // Open modal and load data for Edit.
                $('.datatable').on('click', '.edit-user', function (e) {
                    e.preventDefault();

                    const button = $(this);

                    if (button.data('loading')) {
                        return;
                    }

                    button.data('loading', true);
                    button.prop('disabled', true);

                    const originalText = button.text();
                    const id = button.data('id');

                    button.text('Loading...');

                    resetForm();

                    $.ajax({
                        url: `/users/${id}/data`,
                        method: 'GET',

                        success: function (user) {
                            $('#user_id').val(user.id);
                            $('#first_name').val(user.first_name);
                            $('#last_name').val(user.last_name);
                            $('#email').val(user.email);
                            $('#group_id').val(user.group_id ? String(user.group_id) : '');

                            $('#role_id').val(
                                user.role_id ? String(user.role_id) : ''
                            );

                            // Password is optional during editing.
                            $('#password')
                                .val('')
                                .prop('required', false);

                            $('#confirm_password')
                                .val('')
                                .prop('required', false);

                            $('#modalTitle').text('Edit User');
                            $('#saveUser').text('Update');

                            modal.show();
                        },

                        error: function (xhr) {
                            alert(
                                xhr.responseJSON?.message ||
                                'Unable to load user.'
                            );
                        },

                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                // Create or Update.
                $('#userForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#user_id').val();

                    const url = id
                        ? `/users/${id}`
                        : '{{ route("users.store") }}';

                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors')
                        .addClass('d-none')
                        .empty();

                    $('#saveUser')
                        .prop('disabled', true)
                        .text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),

                        success: function (response) {
                            modal.hide();

                            table.ajax.reload(null, false);

                            $('#successMessage')
                                .text(response.message)
                                .removeClass('d-none');

                            setTimeout(function () {
                                $('#successMessage').addClass('d-none');
                            }, 3000);
                        },

                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let messages = '';

                                Object.values(errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages +=
                                            $('<div>')
                                                .text(message)
                                                .prop('outerHTML');
                                    });
                                });

                                $('#formErrors')
                                    .html(messages)
                                    .removeClass('d-none');
                            } else {
                                alert(
                                    xhr.responseJSON?.message ||
                                    'Unable to save user.'
                                );
                            }
                        },

                        complete: function () {
                            $('#saveUser')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
                        }
                    });
                });

                // Delete using Bootstrap confirmation modal.
                const deleteModalElement =
                    document.getElementById('deleteUserModal');

                const deleteModal =
                    new bootstrap.Modal(deleteModalElement);

                let deleteUserId = null;

                $('.datatable').on('click', '.delete-user', function (e) {
                    e.preventDefault();

                    if ($('#confirmDeleteUser').prop('disabled')) {
                        return;
                    }

                    deleteUserId = $(this).data('id');

                    deleteModal.show();
                });

                // Confirm deletion.
                $('#confirmDeleteUser').on('click', function () {
                    if (!deleteUserId) {
                        return;
                    }

                    const button = $(this);

                    if (button.prop('disabled')) {
                        return;
                    }

                    button
                        .prop('disabled', true)
                        .text('Deleting...');

                    $.ajax({
                        url: `/users/${deleteUserId}`,
                        method: 'DELETE',

                        data: {
                            _token: '{{ csrf_token() }}'
                        },

                        success: function (response) {
                            deleteModal.hide();

                            table.ajax.reload(null, false);

                            $('#successMessage')
                                .text(response.message)
                                .removeClass('d-none');

                            setTimeout(function () {
                                $('#successMessage').addClass('d-none');
                            }, 3000);
                        },

                        error: function (xhr) {
                            alert(
                                xhr.responseJSON?.message ||
                                'Unable to delete user.'
                            );
                        },

                        complete: function () {
                            button
                                .prop('disabled', false)
                                .text('Delete');

                            deleteUserId = null;
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>