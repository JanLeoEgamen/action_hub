<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Companies"
            action-label="Create"
            action-id="create-company"
            permission="create companies"
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
                                <th>Company Name</th>
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
    <div class="modal fade" id="create-company-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="create-company-form">
                    @csrf
                    <input type="hidden" id="create-company_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalTitle">Create Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="create-company_name" class="form-label">Company Name</label>
                            <input type="text" name="company_name" id="create-company_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="create-company_description" class="form-label">Description</label>
                            <textarea name="company_description" id="create-company_description" class="form-control" rows="3"></textarea>
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
                        <button type="submit" id="saveCreateCompany" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-company-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-company-form">
                    @csrf
                    <input type="hidden" id="edit-company_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Edit Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="edit-company_name" class="form-label">Company Name</label>
                            <input type="text" name="company_name" id="edit-company_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="edit-company_description" class="form-label">Description</label>
                            <textarea name="company_description" id="edit-company_description" class="form-control" rows="3"></textarea>
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
                        <button type="submit" id="saveEditCompany" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-delete-confirmation-modal
        id="deleteCompanyModal"
        title="Confirm Delete"
        message="Are you sure you want to delete this company?"
        confirm-button-id="confirmDeleteCompany"
        confirm-label="Delete"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModal = new bootstrap.Modal(document.getElementById('create-company-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-company-modal'));
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteCompanyModal'));
                let deleteId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("companies.index") }}',
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'company_name' },
                        { data: 'company_description' },
                        { data: 'is_active' },
                        { data: 'created_at' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                function resetCreateForm() {
                    $('#create-company-form')[0].reset();
                    $('#create-company_id').val('');
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#create-is_active').val('1');
                }

                function resetEditForm() {
                    $('#edit-company-form')[0].reset();
                    $('#edit-company_id').val('');
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#edit-is_active').val('1');
                }

                $('#create-company').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                $('.datatable').on('click', '.edit-company', function () {
                    const id = $(this).data('id');
                    resetEditForm();
                    $.ajax({
                        url: `/companies/${id}/edit`,
                        method: 'GET',
                        success: function (data) {
                            $('#edit-company_id').val(data.id);
                            $('#edit-company_name').val(data.company_name);
                            $('#edit-company_description').val(data.company_description);
                            $('#edit-is_active').val(data.is_active);
                            editModal.show();
                        },
                        error: function () { alert('Unable to load data.'); }
                    });
                });

                $('#create-company-form').on('submit', function (e) {
                    e.preventDefault();
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateCompany').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("companies.store") }}',
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
                                Object.values(xhr.responseJSON.errors).forEach(errs => {
                                    errs.forEach(msg => messages += `<div>${msg}</div>`);
                                });
                                $('#create-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            $('#saveCreateCompany').prop('disabled', false).text('Create');
                        }
                    });
                });

                $('#edit-company-form').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#edit-company_id').val();
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditCompany').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/companies/${id}`,
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
                                Object.values(xhr.responseJSON.errors).forEach(errs => {
                                    errs.forEach(msg => messages += `<div>${msg}</div>`);
                                });
                                $('#edit-formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            $('#saveEditCompany').prop('disabled', false).text('Update');
                        }
                    });
                });

                $('.datatable').on('click', '.delete-company', function () {
                    deleteId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteCompany').on('click', function () {
                    if (!deleteId) return;
                    const btn = $(this).prop('disabled', true).text('Deleting...');

                    $.ajax({
                        url: `/companies/${deleteId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            deleteModal.hide();
                            table.ajax.reload(null, false);
                            $('#successMessage').text(response.message).removeClass('d-none');
                            setTimeout(() => $('#successMessage').addClass('d-none'), 3000);
                        },
                        error: function () { alert('Unable to delete.'); },
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
