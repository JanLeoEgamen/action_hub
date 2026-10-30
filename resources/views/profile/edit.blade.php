<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div id="successMessage" class="alert alert-success d-none"></div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Edit Confirmation Modal for Profile Information -->
            <x-edit-confirmation-modal
                id="editConfirmProfileModal"
                title="Confirm Edit"
                message="Are you sure you want to update your profile information?"
                confirm-button-id="confirmEditProfile"
                confirm-label="Save Changes"
            />

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Edit Confirmation Modal for Password -->
            <x-edit-confirmation-modal
                id="editConfirmPasswordModal"
                title="Confirm Edit"
                message="Are you sure you want to change your password?"
                confirm-button-id="confirmEditPassword"
                confirm-label="Save Password"
            />

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function () {
            const editConfirmProfileModal = new bootstrap.Modal(document.getElementById('editConfirmProfileModal'));
            const editConfirmPasswordModal = new bootstrap.Modal(document.getElementById('editConfirmPasswordModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('delete-user-modal'));

            let pendingProfileForm = null;
            let pendingPasswordForm = null;

            // Profile Information form — confirm before submit
            $('form[action*="profile.update"]').on('submit', function (e) {
                e.preventDefault();
                pendingProfileForm = this;
                editConfirmProfileModal.show();
            });

            // Password form — confirm before submit
            $('form[action*="password.update"]').on('submit', function (e) {
                e.preventDefault();
                pendingPasswordForm = this;
                editConfirmPasswordModal.show();
            });

            // Confirm Profile Information edit
            $('#confirmEditProfile').on('click', function () {
                if (!pendingProfileForm) return;
                const btn = $(this).prop('disabled', true).text('Saving...');
                editConfirmProfileModal.hide();
                pendingProfileForm.submit();
                btn.prop('disabled', false).text('Save Changes');
                pendingProfileForm = null;
            });

            // Confirm Password edit
            $('#confirmEditPassword').on('click', function () {
                if (!pendingPasswordForm) return;
                const btn = $(this).prop('disabled', true).text('Saving...');
                editConfirmPasswordModal.hide();
                pendingPasswordForm.submit();
                btn.prop('disabled', false).text('Save Password');
                pendingPasswordForm = null;
            });

            // Open confirmation modal
            $('#delete-account-button').on('click', function () {
                $('#delete-formErrors').addClass('d-none').empty();
                deleteModal.show();
            });

            // Submit delete form via AJAX
            $('#delete-user-form').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const btn = $('#confirmDeleteAccount');

                $('#delete-formErrors').addClass('d-none').empty();
                btn.prop('disabled', true).text('{{ __('Deleting...') }}');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                }).done(function (response) {
                    deleteModal.hide();
                    $('#successMessage').text(response.message).removeClass('d-none');
                    setTimeout(function () {
                        $('#successMessage').addClass('d-none');
                        window.location.href = '/';
                    }, 3000);
                }).fail(function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors ?? {};
                        let messages = '';
                        Object.values(errors).forEach(function (fieldErrors) {
                            fieldErrors.forEach(function (message) {
                                messages += '<div>' + message + '</div>';
                            });
                        });
                        $('#delete-formErrors').html(messages).removeClass('d-none');
                    } else {
                        alert(xhr.responseJSON?.message || 'Unable to delete account.');
                    }
                }).always(function () {
                    btn.prop('disabled', false).text('{{ __('Delete Account') }}');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
