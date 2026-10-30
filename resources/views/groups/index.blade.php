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
                                <th>Status</th>
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
    <div class="modal fade" id="create-group-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="create-group-form">
                    @csrf
                    <input type="hidden" id="create-group_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalTitle">Create Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="create-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="create-department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->company_name }} - {{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="group_name" id="create-group_name" class="form-control" required maxlength="255">
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
                        <button type="submit" id="saveCreateGroup" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="edit-group-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-group-form">
                    @csrf
                    <input type="hidden" id="edit-group_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Edit Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-formErrors" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="edit-department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->company_name }} - {{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="group_name" id="edit-group_name" class="form-control" required maxlength="255">
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
                        <button type="submit" id="saveEditGroup" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-delete-confirmation-modal id="deleteGroupModal" title="Confirm Delete" message="Are you sure you want to delete this group?" confirm-button-id="confirmDeleteGroup" confirm-label="Delete" />

    <!-- Edit Confirmation Modal -->
    <x-edit-confirmation-modal
        id="editConfirmGroupModal"
        title="Confirm Edit"
        message="Are you sure you want to edit this group?"
        confirm-button-id="confirmEditGroup"
        confirm-label="Edit"
    />

    @push('scripts')
        <script>
            $(document).ready(function () {
                const createModal = new bootstrap.Modal(document.getElementById('create-group-modal'));
                const editModal = new bootstrap.Modal(document.getElementById('edit-group-modal'));
                const editConfirmModal = new bootstrap.Modal(document.getElementById('editConfirmGroupModal'));
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteGroupModal'));
                let deleteId = null;
                let editConfirmId = null;

                const table = $('.datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("groups.index") }}',
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'company_name' },
                        { data: 'department_name' },
                        { data: 'group_name' },
                        { data: 'is_active' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                function resetCreateForm() {
                    $('#create-group-form')[0].reset();
                    $('#create-group_id').val('');
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#create-is_active').val('1');
                }

                function resetEditForm() {
                    $('#edit-group-form')[0].reset();
                    $('#edit-group_id').val('');
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#edit-is_active').val('1');
                }

                $('#create-group').on('click', function () {
                    resetCreateForm();
                    createModal.show();
                });

                $('.datatable').on('click', '.edit-group', function () {
                    const id = $(this).data('id');
                    resetEditForm();
                    $.ajax({
                        url: `/groups/${id}/edit`,
                        method: 'GET',
                        success: function (data) {
                            $('#edit-group_id').val(data.id);
                            $('#edit-department_id').val(data.department_id);
                            $('#edit-group_name').val(data.group_name);
                            $('#edit-is_active').val(data.is_active);
                            editModal.show();
                        },
                        error: function () { alert('Unable to load data.'); }
                    });
                });

                // Confirm Edit before saving
                $('#edit-group-form').on('submit', function (e) {
                    e.preventDefault();
                    editConfirmId = $('#edit-group_id').val();
                    editConfirmModal.show();
                });

                $('#confirmEditGroup').on('click', function () {
                    if (!editConfirmId) return;
                    editConfirmModal.hide();

                    const id = $('#edit-group_id').val();
                    $('#edit-formErrors').addClass('d-none').empty();
                    $('#saveEditGroup').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: `/groups/${id}`,
                        method: 'PUT',
                        data: $('#edit-group-form').serialize(),
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
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            $('#saveEditGroup').prop('disabled', false).text('Update');
                            editConfirmId = null;
                        }
                    });
                });

                $('#create-group-form').on('submit', function (e) {
                    e.preventDefault();
                    $('#create-formErrors').addClass('d-none').empty();
                    $('#saveCreateGroup').prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: '{{ route("groups.store") }}',
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
                            $('#saveCreateGroup').prop('disabled', false).text('Create');
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
