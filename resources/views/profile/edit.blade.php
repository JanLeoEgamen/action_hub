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

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

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
            const deleteModal = new bootstrap.Modal(document.getElementById('delete-user-modal'));

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
