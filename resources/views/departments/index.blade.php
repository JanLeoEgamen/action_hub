<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Departments" action-label="Create" action-id="create-department" permission="create departments" />
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
                                <th>Company</th>
                                <th>Department Name</th>
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
    <div class="modal fade" id="create-department-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="create-department-form">
                    @csrf
                    <input type="hidden" id="create-department_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalTitle">Create Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" id="create-company_id" class="form-select" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="department_name" id="create-department_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="department_description" id="create-department_description" class="form-control" rows="3"></textarea>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCreateDepartment" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-department-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-department-form">
                    @csrf
                    <input type="hidden" id="edit-department_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Edit Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" id="edit-company_id" class="form-select" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="department_name" id="edit-department_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="department_description" id="edit-department_description" class="form-control" rows="3"></textarea>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveEditDepartment" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-delete-confirmation-modal id="deleteDepartmentModal" title="Confirm Delete" message="Are you sure you want to delete this department?" confirm-button-id="confirmDeleteDepartment" confirm-label="Delete" />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModal = new bootstrap.Modal(document.getElementById('create-department-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-department-modal'));
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteDepartmentModal'));
                let deleteId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("departments.index") }}',
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'company_name' },
                        { data: 'department_name' },
                        { data: 'department_description' },
                        { data: 'is_active' },
                        { data: 'created_at' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                function resetCreateForm() {
                    $('#create-department-form')[0].reset();
                    $('#create-department_id').val('');
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#create-is_active').val('1');
                }

                function resetEditForm() {
                    $('#edit-department-form')[0].reset();
                    $('#edit-department_id').val('');
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#edit-is_active').val('1');
                }

                $('#create-department').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                $('.datatable').on('click', '.edit-department', function () {
                    const id = $(this).data('id');
                    resetEditForm();
                    $.ajax({
                        url: `/departments/${id}/edit`,
                        method: 'GET',
                        success: function (data) {
                            $('#edit-department_id').val(data.id);
                            $('#edit-company_id').val(data.company_id);
                            $('#edit-department_name').val(data.department_name);
                            $('#edit-department_description').val(data.department_description);
                            $('#edit-is_active').val(data.is_active);
                            editModal.show();
                        }
                    });
                });

                $('#create-department-form').on('submit', function (e) {
                    e.preventDefault();
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateDepartment').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("departments.store") }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            createModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
                            setTimeout(() => $('#successMessage').addClass('d-none'), 3000);
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                Object.values(xhr.responseJSON.errors).forEach(errs => errs.forEach(msg => messages += `<div>${msg}</div>`));
                                $('#create-formErrors').html(messages).removeClass('d-none');
                            }
                        },
                        complete: function () {
                            $('#saveCreateDepartment').prop('disabled', false).text('Create');
                        }
                    });
                });

                $('#edit-department-form').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#edit-department_id').val();
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditDepartment').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/departments/${id}`,
                        method: 'PUT',
                        data: $(this).serialize(),
                        success: function (response) {
                            editModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
                            setTimeout(() => $('#successMessage').addClass('d-none'), 3000);
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                Object.values(xhr.responseJSON.errors).forEach(errs => errs.forEach(msg => messages += `<div>${msg}</div>`));
                                $('#edit-formErrors').html(messages).removeClass('d-none');
                            }
                        },
                        complete: function () {
                            $('#saveEditDepartment').prop('disabled', false).text('Update');
                        }
                    });
                });

                $('.datatable').on('click', '.delete-department', function () {
                    deleteId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteDepartment').on('click', function () {
                    if (!deleteId) return;
                    const btn = $(this).prop('disabled', true).text('Deleting...');
                    $.ajax({
                        url: `/departments/${deleteId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            deleteModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
                            setTimeout(() => $('#successMessage').addClass('d-none'), 3000);
                        },
                        complete: function () {
                            btn.prop('disabled', false).text('Delete');
                            deleteId = null;
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
