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

    <!-- Create Modal -->
    <div class="modal fade" id="create-user-modal" tabindex="-1"
        aria-labelledby="createModalTitle" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <form id="create-user-form">
                    @csrf

                    <input type="hidden" id="create-user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalTitle">
                            Create User
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="create-formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="create-first_name" class="form-label">
                                First Name
                            </label>

                            <input type="text"
                                name="first_name"
                                id="create-first_name"
                                class="form-control"
                                required
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="create-last_name" class="form-label">
                                Last Name
                            </label>

                            <input type="text"
                                name="last_name"
                                id="create-last_name"
                                class="form-control"
                                required
                                minlength="4"
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="create-email" class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                name="email"
                                id="create-email"
                                class="form-control"
                                required
                                maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="create-group_id" class="form-label">Group</label>
                            <select name="group_id" id="create-group_id" class="form-select">
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

                            <div id="create-roleCheckboxes" class="border rounded p-3">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            id="create-role_{{ $role->id }}"
                                            value="{{ $role->id }}"
                                            class="form-check-input role-checkbox"
                                        >

                                        <label
                                            for="create-role_{{ $role->id }}"
                                            class="form-check-label"
                                        >
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="create-password" class="form-label">
                                Password
                            </label>

                            <input type="password"
                                name="password"
                                id="create-password"
                                class="form-control"
                                required
                                minlength="8"
                                autocomplete="new-password">


                        </div>

                        <div class="mb-3">
                            <label for="create-confirm_password" class="form-label">
                                Confirm Password
                            </label>

                            <input type="password"
                                name="confirm_password"
                                id="create-confirm_password"
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
                            id="saveCreateUser"
                            class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-user-modal" tabindex="-1"
        aria-labelledby="editModalTitle" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-user-form">
                    @csrf

                    <input type="hidden" id="edit-user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">
                            Edit User
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="edit-formErrors"
                            class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="edit-first_name" class="form-label">
                                First Name
                            </label>

                            <input type="text"
                                name="first_name"
                                id="edit-first_name"
                                class="form-control"
                                required
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="edit-last_name" class="form-label">
                                Last Name
                            </label>

                            <input type="text"
                                name="last_name"
                                id="edit-last_name"
                                class="form-control"
                                required
                                minlength="4"
                                maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="edit-email" class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                name="email"
                                id="edit-email"
                                class="form-control"
                                required
                                maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="edit-group_id" class="form-label">Group</label>
                            <select name="group_id" id="edit-group_id" class="form-select">
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

                            <div id="edit-roleCheckboxes" class="border rounded p-3">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            id="edit-role_{{ $role->id }}"
                                            value="{{ $role->id }}"
                                            class="form-check-input role-checkbox"
                                        >

                                        <label
                                            for="edit-role_{{ $role->id }}"
                                            class="form-check-label"
                                        >
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit-password" class="form-label">
                                Password
                            </label>

                            <input type="password"
                                name="password"
                                id="edit-password"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password">


                        </div>

                        <div class="mb-3">
                            <label for="edit-confirm_password" class="form-label">
                                Confirm Password
                            </label>

                            <input type="password"
                                name="confirm_password"
                                id="edit-confirm_password"
                                class="form-control"
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
                            id="saveEditUser"
                            class="btn btn-primary">
                            Update
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

    <!-- Edit Confirmation Modal -->
    <x-edit-confirmation-modal
        id="editConfirmUserModal"
        title="Confirm Edit"
        message="Are you sure you want to edit this user?"
        confirm-button-id="confirmEditUser"
        confirm-label="Edit"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModalElement = document.getElementById('create-user-modal');
                const createModal = new bootstrap.Modal(createModalElement);

                const editModalElement = document.getElementById('edit-user-modal');
                const editModal = new bootstrap.Modal(editModalElement);

                const editConfirmModalElement = document.getElementById('editConfirmUserModal');
                const editConfirmModal = new bootstrap.Modal(editConfirmModalElement);

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

                function resetCreateForm() {
                    $('#create-user-form')[0].reset();
                    $('#create-user_id').val('');
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#create-password, #create-confirm_password').prop('required', true);
                }

                // Open modal for Create.
                $('#create-user').on('click', function () {
                    resetCreateForm();
                    createModal.show();
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

                    $.ajax({
                        url: `/users/${id}/edit`,
                        method: 'GET',

                        success: function (user) {
                            $('#edit-user_id').val(user.id);
                            $('#edit-first_name').val(user.first_name);
                            $('#edit-last_name').val(user.last_name);
                            $('#edit-email').val(user.email);
                            $('#edit-group_id').val(user.group_id ? String(user.group_id) : '');

                            $('#edit-roleCheckboxes input[type="checkbox"]').prop('checked', false);
                            if (user.role_id) {
                                $('#edit-role_' + user.role_id).prop('checked', true);
                            }

                            $('#edit-password').val('').prop('required', false);
                            $('#edit-confirm_password').val('').prop('required', false);

                            editModal.show();
                        },

                        error: function (xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to load user.');
                        },

                        complete: function () {
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });

                // Create form submit.
                $('#create-user-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateUser').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("users.store") }}',
                        method: 'POST',
                        data: $(this).serialize(),

                        success: function (response) {
                            createModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
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
                                        messages += $('<div>').text(message).prop('outerHTML');
                                    });
                                });
                                $('#create-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save user.');
                            }
                        },

                        complete: function () {
                            $('#saveCreateUser').prop('disabled', false).text('Create');
                        }
                    });
                });

                // Edit form submit.
                // Confirm Edit before saving
                $('#edit-user-form').on('submit', function (e) {
                    e.preventDefault();
                    editConfirmUserId = $('#edit-user_id').val();
                    editConfirmModal.show();
                });

                $('#confirmEditUser').on('click', function () {
                    if (!editConfirmUserId) return;
                    editConfirmModal.hide();

                    const id = $('#edit-user_id').val();

                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditUser').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/users/${id}`,
                        method: 'PUT',
                        data: $('#edit-user-form').serialize(),

                        success: function (response) {
                            editModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
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
                                        messages += $('<div>').text(message).prop('outerHTML');
                                    });
                                });
                                $('#edit-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save user.');
                            }
                        },

                        complete: function () {
                            $('#saveEditUser').prop('disabled', false).text('Update');
                        }
                    });

                    editConfirmUserId = null;
                });
                // Delete using Bootstrap confirmation modal.
                const deleteModalElement =
                    document.getElementById('deleteUserModal');

                const deleteModal =
                    new bootstrap.Modal(deleteModalElement);

                let deleteUserId = null;
                let editConfirmUserId = null;

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

