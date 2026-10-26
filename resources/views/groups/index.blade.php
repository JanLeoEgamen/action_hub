<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Groups" action-label="Create" action-id="create-group" permission="create groups" />
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
                                <th>Department</th>
                                <th>Group Name</th>
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
    <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="groupForm">
                    @csrf
                    <input type="hidden" id="group_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Create Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formErrors" class="alert alert-danger d-none"></div>
                        
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->company_name }} - {{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="group_name" id="group_name" class="form-control" required maxlength="255">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveGroup" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-delete-confirmation-modal id="deleteGroupModal" title="Confirm Delete" message="Are you sure you want to delete this group?" confirm-button-id="confirmDeleteGroup" confirm-label="Delete" />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const modal = new bootstrap.Modal(document.getElementById('groupModal'));
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteGroupModal'));
                let deleteId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("groups.index") }}',
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'company_name' },
                        { data: 'department_name' },
                        { data: 'group_name' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                function resetForm() {
                    $('#groupForm')[0].reset();
                    $('#group_id').val('');
                    $('#formErrors').addClass('d-none').empty();
                    $('#is_active').prop('checked', true);
                }

                $('#create-group').on('click', function () {
                    resetForm();
                    $('#modalTitle').text('Create Group');
                    $('#saveGroup').text('Create');
                    modal.show();
                });

                $('.datatable').on('click', '.edit-group', function () {
                    const id = $(this).data('id');
                    resetForm();
                    $.ajax({
                        url: `/groups/${id}/data`,
                        method: 'GET',
                        success: function (data) {
                            $('#group_id').val(data.id);
                            $('#department_id').val(data.department_id);
                            $('#group_name').val(data.group_name);
                            $('#is_active').prop('checked', data.is_active == 1);
                            
                            $('#modalTitle').text('Edit Group');
                            $('#saveGroup').text('Update');
                            modal.show();
                        }
                    });
                });

                $('#groupForm').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#group_id').val();
                    const url = id ? `/groups/${id}` : '{{ route("groups.store") }}';
                    const method = id ? 'PUT' : 'POST';

                    $('#formErrors').addClass('d-none').empty();
                    $('#saveGroup').prop('disabled', true).text('Saving...');

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
                            $('#saveGroup').prop('disabled', false).text(id ? 'Update' : 'Create');
                        }
                    });
                });

                $('.datatable').on('click', '.delete-group', function () {
                    deleteId = $(this).data('id');
                    deleteModal.show();
                });

                $('#confirmDeleteGroup').on('click', function () {
                    if (!deleteId) return;
                    const btn = $(this).prop('disabled', true).text('Deleting...');
                    $.ajax({
                        url: `/groups/${deleteId}`,
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