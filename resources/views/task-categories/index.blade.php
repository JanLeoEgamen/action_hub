
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Categories') }}
            </h2>

            <button type="button" id="create-taskCategory"
                class="bg-slate-700 text-sm rounded-md px-5 py-3 text-white hover:bg-slate-600">
                Create
            </button>
        </div>
    </x-slot>

    <div class="container py-5">
        <div id="successMessage" class="alert alert-success d-none"></div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Categories</h5>
            </div>

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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="taskCategoryModal" tabindex="-1"
        aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="taskCategoryForm">
                    @csrf

                    <input type="hidden" id="category_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Create Task Category</h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" name="name" id="name"
                                class="form-control" required minlength="4" maxlength="30">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description"
                                class="form-control" required minlength="4" maxlength="30"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select name="is_active" id="is_active" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCategory"
                            class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1"
        aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete this category?
                    This action cannot be undone.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button" class="btn btn-danger"
                        id="confirmDeleteCategory">
                        Delete
                    </button>
                </div>

            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function () {
                const modalElement = document.getElementById('taskCategoryModal');
                const modal = new bootstrap.Modal(modalElement);

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

                function resetForm() {
                    $('#taskCategoryForm')[0].reset();
                    $('#category_id').val('');
                    $('#formErrors').addClass('d-none').empty();
                }

                // Open modal for Create
                $('#create-taskCategory').on('click', function () {
                    resetForm();
                    $('#modalTitle').text('Create Task Category');
                    $('#saveCategory').text('Create');
                    modal.show();
                });


                // Open modal and load data for Edit
                $('.datatable').on('click', '.edit-taskCategory', function (e) {
                    e.preventDefault();

                    const button = $(this);

                    // Prevent spam clicks
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
                        url: `/task-categories/${id}/data`,
                        method: 'GET',

                        success: function (category) {
                            $('#category_id').val(category.id);
                            $('#name').val(category.name);
                            $('#description').val(category.description);
                            $('#is_active').val(String(category.is_active));

                            $('#modalTitle').text('Edit Task Category');
                            $('#saveCategory').text('Update');

                            modal.show();
                        },

                        error: function () {
                            alert('Unable to load task category.');
                        },

                        complete: function () {
                            // Re-enable the button when the request finishes
                            button.prop('disabled', false);
                            button.text(originalText);
                            button.removeData('loading');
                        }
                    });
                });


                // Create or Update
                $('#taskCategoryForm').on('submit', function (e) {
                    e.preventDefault();

                    const id = $('#category_id').val();
                    const url = id
                        ? `/task-categories/${id}`
                        : '{{ route("task-categories.store") }}';

                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveCategory').prop('disabled', true).text('Saving...');

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
                                        messages += `<div>${message}</div>`;
                                    });
                                });

                                $('#formErrors')
                                    .html(messages)
                                    .removeClass('d-none');
                            } else {
                                alert(xhr.responseJSON?.message || 'Unable to save category.');
                            }
                        },

                        complete: function () {
                            $('#saveCategory')
                                .prop('disabled', false)
                                .text(id ? 'Update' : 'Create');
                        }
                    });
                });


            // Soft-delete using Bootstrap confirmation modal
            const deleteModalElement = document.getElementById('deleteCategoryModal');
            const deleteModal = new bootstrap.Modal(deleteModalElement);

            let deleteCategoryId = null;
            let deleteButton = null;

            // Open confirmation modal
            $('.datatable').on('click', '.delete-taskCategory', function (e) {
                e.preventDefault();

                // Prevent repeated clicks while deleting
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

                // Prevent duplicate delete requests
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
                            xhr.responseJSON?.message ||
                            'Unable to delete category.'
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
    @endpush
</x-app-layout>
