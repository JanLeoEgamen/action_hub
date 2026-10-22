
@props([
    'id' => 'deleteModal',
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this record? This action cannot be undone.',
    'confirmButtonId' => 'confirmDelete',
    'confirmLabel' => 'Delete',
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
                    class="btn btn-danger"
                    id="{{ $confirmButtonId }}"
                >
                    {{ $confirmLabel }}
                </button>
            </div>

        </div>
    </div>
</div>
