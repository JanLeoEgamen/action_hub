@props([
    'id' => 'editConfirmModal',
    'title' => 'Confirm Edit',
    'message' => 'Are you sure you want to edit this record?',
    'confirmButtonId' => 'confirmEdit',
    'confirmLabel' => 'Edit',
])

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form
                id="{{ $id }}-form"
                method="POST"
            >
                @csrf

                <div class="modal-header">
                    <h5
                        class="modal-title"
                        id="{{ $id }}Label"
                    >
                        {{ $title }}
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    <div id="{{ $id }}-formErrors" class="alert alert-danger d-none"></div>

                    {{ $message }}
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="{{ $confirmButtonId }}"
                    >
                        {{ $confirmLabel }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>