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

    <!-- Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="departmentForm">
                    @csrf
                    <input type="hidden" id="department_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Create Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>
                        
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" id="company_id" class="form-select" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="department_name" id="department_name" class="form-control" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="department_description" id="department_description" class="form-control" rows="3"></textarea>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveDepartment" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-delete-confirmation-modal id="deleteDepartmentModal" title="Confirm Delete" message="Are you sure you want to delete this department?" confirm-button-id="confirmDeleteDepartment" confirm-label="Delete" />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const modal = new bootstrap.Modal(document.getElementById('departmentModal'));
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

                function resetForm() {
                    $('#departmentForm')[0].reset();
                    $('#department_id').val('');
                    $('#formErrors').addClass('d-none').empty();
                    $('#is_active').val('1');
                }

                $('#create-department').on('click', function () {
                    resetForm();
                    $('#modalTitle').text('Create Department');
                    $('#saveDepartment').text('Create');
                    modal.show();
                });

                $('.datatable').on('click', '.edit-department', function () {
                    const id = $(this).data('id');
                    resetForm();
                    $.ajax({
                        url: `/departments/${id}/data`,
                        method: 'GET',
                        success: function (data) {
                            $('#department_id').val(data.id);
                            $('#company_id').val(data.company_id);
                            $('#department_name').val(data.department_name);
                            $('#department_description').val(data.department_description);
                            $('#is_active').val(data.is_active);
                            
                            $('#modalTitle').text('Edit Department');
                            $('#saveDepartment').text('Update');
                            modal.show();
                        }
                    });
                });

                $('#departmentForm').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#department_id').val();
                    const url = id ? `/departments/${id}` : '{{ route("departments.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveDepartment').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function (response) {
                            modal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
                            setTimeout(() => $('#successMessage').addClass('d-none'), 3000);
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let messages = '';
                                Object.values(xhr.responseJSON.errors).forEach(errs => errs.forEach(msg => messages += `<div>${msg}</div>`));
                                $('#formErrors').html(messages).removeClass('d-none');
                            }
                        },
                        complete: function () {
                            $('#saveDepartment').prop('disabled', false).text(id ? 'Update' : 'Create');
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