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
    <div class="modal fade" id="companyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="companyForm">
                    @csrf
                    <input type="hidden" id="company_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Create Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>
                        
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="company_description" class="form-label">Description</label>
                            <textarea name="company_description" id="company_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveCompany" class="btn btn-primary">Create</button>
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
                const modal = new bootstrap.Modal(document.getElementById('companyModal'));
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteCompanyModal'));
                let deleteId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("companies.index") }}',
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'company_name' },
                        { data: 'is_active' },
                        { data: 'created_at' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                function resetForm() {
                    $('#companyForm')[0].reset();
                    $('#company_id').val('');
                    $('#formErrors').addClass('d-none').empty();
                    $('#is_active').prop('checked', true);
                }

                $('#create-company').on('click', function () {
                    resetForm();
                    $('#modalTitle').text('Create Company');
                    $('#saveCompany').text('Create');
                    modal.show();
                });

                $('.datatable').on('click', '.edit-company', function () {
                    const id = $(this).data('id');
                    resetForm();
                    $.ajax({
                        url: `/companies/${id}/data`,
                        method: 'GET',
                        success: function (data) {
                            $('#company_id').val(data.id);
                            $('#company_name').val(data.company_name);
                            $('#company_description').val(data.company_description);
                            $('#is_active').prop('checked', data.is_active == 1);
                            
                            $('#modalTitle').text('Edit Company');
                            $('#saveCompany').text('Update');
                            modal.show();
                        },
                        error: function () { alert('Unable to load data.'); }
                    });
                });

                $('#companyForm').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#company_id').val();
                    const url = id ? `/companies/${id}` : '{{ route("companies.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveCompany').prop('disabled', true).text('Saving...');

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
                                Object.values(xhr.responseJSON.errors).forEach(errs => {
                                    errs.forEach(msg => messages += `<div>${msg}</div>`);
                                });
                                $('#formErrors').html(messages).removeClass('d-none');
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            $('#saveCompany').prop('disabled', false).text(id ? 'Update' : 'Create');
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