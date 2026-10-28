<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        id="delete-account-button"
        class="bg-red-600 text-white px-5 py-3 text-sm rounded-md hover:bg-red-700"
    >
        {{ __('Delete Account') }}
    </button>

    <!-- Delete Confirmation Modal (Bootstrap Pattern A) -->
    <div class="modal fade" id="delete-user-modal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">{{ __('Confirm Delete Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <form id="delete-user-form" method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body">
                        <div id="delete-formErrors" class="alert alert-danger d-none"></div>

                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Are you sure you want to delete your account? This action cannot be undone.') }}
                        </p>

                        <div class="mb-3">
                            <x-input-label for="delete-password" value="{{ __('Password') }}" class="sr-only" />

                            <x-text-input
                                id="delete-password"
                                name="password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="{{ __('Password') }}"
                                required
                            />

                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" id="confirmDeleteAccount" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
