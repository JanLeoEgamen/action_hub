
<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Task Categories"
            action-label="Create"
            action-id="create-taskCategory"
            permission="create task categories"
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
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Status</th>
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
    <div class="modal fade" id="create-taskCategory-modal" tabindex="-1"
        aria-labelledby="createModalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="create-taskCategory-form">
                    @csrf

                    <input type="hidden" id="create-category_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalTitle">Create Task Category</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="create-name" class="form-label">Category Name</label>
                            <input type="text" name="name" id="create-name"
                                class="form-control" required minlength="4" maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="create-description" class="form-label">Description</label>
                            <textarea name="description" id="create-description"
                                class="form-control" required minlength="4" maxlength="30"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="create-is_active" class="form-label">Status</label>
                            <select name="is_active" id="create-is_active" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCreateCategory"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-taskCategory-modal" tabindex="-1"
        aria-labelledby="editModalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-taskCategory-form">
                    @csrf

                    <input type="hidden" id="edit-category_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Edit Task Category</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Category Name</label>
                            <input type="text" name="name" id="edit-name"
                                class="form-control" required minlength="4" maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Description</label>
                            <textarea name="description" id="edit-description"
                                class="form-control" required minlength="4" maxlength="30"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit-is_active" class="form-label">Status</label>
                            <select name="is_active" id="edit-is_active" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveEditCategory"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        id="deleteCategoryModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this category?"
        confirm-button-id="confirmDeleteCategory"
        confirm-label="Delete"
    />

    <!-- Edit Confirmation Modal -->
    <x-edit-confirmation-modal
        id="editConfirmCategoryModal"
        title="Confirm Edit"
        message="Are you sure you want to edit this task category?"
        confirm-button-id="confirmEditCategory"
        confirm-label="Save Task Category"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModalElement = document.getElementById('create-taskCategory-modal');
                const createModal = new bootstrap.Modal(createModalElement);

                const editModalElement = document.getElementById('edit-taskCategory-modal');
                const editModal = new bootstrap.Modal(editModalElement);

                const editConfirmModalElement = document.getElementById('editConfirmCategoryModal');
                const editConfirmModal = new bootstrap.Modal(editConfirmModalElement);

                const deleteModalElement = document.getElementById('deleteCategoryModal');
                const deleteModal = new bootstrap.Modal(deleteModalElement);

                let deleteCategoryId = null;
                let deleteButton = null;
                let editConfirmCategoryId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: '{{ route("task-categories.index") }}',
                        type: 'GET'
                    },
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        { data: 'name', name: 'name' },
                        { data: 'description', name: 'description' },
                        { data: 'is_active', name: 'is_active' },
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
                    $('#create-taskCategory-form')[0].reset();
                    $('#create-category_id').val('');
                    $('#create-formErrors').addClass('d-none').empty();
                }

                function resetEditForm() {
                    $('#edit-taskCategory-form')[0].reset();
                    $('#edit-category_id').val('');
                    $('#edit-formErrors').addClass('d-none').empty();
                }

                // Open modal for Create
                $('#create-taskCategory').on('click', function (e) {
                    e.preventDefault();
                    resetCreateForm();
                    createModal.show();
                });

                // Open Edit Confirmation modal for Edit
                $('.datatable').on('click', '.edit-taskCategory', function (e) {
                    e.preventDefault();

                    if ($('#saveEditCategory').prop('disabled')) {
                        return;
                    }

                    const id = $(this).data('id');

                    resetEditForm();

                    $.ajax({
                        url: `/task-categories/${id}/edit`,
                        method: 'GET',

                        success: function (data) {
                            $('#edit-category_id').val(data.id);
                            $('#edit-name').val(data.name);
                            $('#edit-description').val(data.description);
                            $('#edit-is_active').val(data.is_active);

                            editModal.show();
                        },
                        error: function () { alert('Unable to load data.'); }
                    });
                });

                // Confirm Edit before saving
                $('#edit-taskCategory-form').on('submit', function (e) {
                    e.preventDefault();
                    editConfirmCategoryId = $('#edit-category_id').val();
                    editConfirmModal.show();
                });

                $('#confirmEditCategory').on('click', function () {
                    if (!editConfirmCategoryId) return;
                    editConfirmModal.hide();

                    const id = $('#edit-category_id').val();
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditCategory').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/task-categories/${id}`,
                        method: 'PUT',
                        data: $('#edit-taskCategory-form').serialize(),
                        success: function (response) {
                            editModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage')
                                .text(response.message)
                                .removeClass('d-none');
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                const errors = xhr.responseJSON.errors;
                                Object.values(errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages += `<div>${message}</div>`;
                                    });
                                });
                                $('#edit-formErrors')
                                    .html(messages)
                                    .removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save category.');
                            }
                        },
                        complete: function () {
                            $('#saveEditCategory')
                                .prop('disabled', false)
                                .text('Save');
                            editConfirmCategoryId = null;
                        }
                    });
                });

                // Create form submit
                $('#create-taskCategory-form').on('submit', function (e) {
                    e.preventDefault();

                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateCategory').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("task-categories.store") }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            createModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage')
                                .text(response.message)
                                .removeClass('d-none');
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                const errors = xhr.responseJSON.errors;
                                Object.values(errors).forEach(function (fieldErrors) {
                                    fieldErrors.forEach(function (message) {
                                        messages += `<div>${message}</div>`;
                                    });
                                });
                                $('#create-formErrors')
                                    .html(messages)
                                    .removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save category.');
                            }
                        },
                        complete: function () {
                            $('#saveCreateCategory')
                                .prop('disabled', false)
                                .text('Save');
                        }
                    });
                });

                // Soft-delete using Bootstrap confirmation modal
                $('.datatable').on('click', '.delete-taskCategory', function (e) {
                    e.preventDefault();

                    if ($('#confirmDeleteCategory').prop('disabled')) {
                        return;
                    }

                    deleteCategoryId = $(this).data('id');
                    deleteButton = $(this);

                    deleteModal.show();
                });

                // Confirm deletion
                $('#confirmDeleteCategory').on('click', function () {
                    if (!deleteCategoryId) {
                        return;
                    }

                    const button = $(this);

                    if (button.prop('disabled')) {
                        return;
                    }

                    button.prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/task-categories/${deleteCategoryId}`,
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
                        },
                        error: function (xhr) {
                            alert(
                                xhr.responseJSON?.message || 'Unable to delete category.'
                            );
                        },
                        complete: function () {
                            button.prop('disabled', false).text('Delete');

                            deleteCategoryId = null;
                            deleteButton = null;
                        }
                    });
                });
            });
        </script>
    @endpush</x-app-layout>

