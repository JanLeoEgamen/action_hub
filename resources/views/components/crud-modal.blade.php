
@props([
    'id' => 'crudModal',
    'title' => 'Create Record',
    'formId' => 'crudForm',
    'saveButtonId' => 'saveButton',
    'saveText' => 'Save',
])

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    {{ $title }}
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <form id="{{ $formId }}">
                @csrf

                <div class="modal-body">
                    <div
                        id="formErrors"
                        class="alert alert-danger d-none"
                        role="alert"
                    ></div>

                    {{ $slot }}
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
                        type="submit"
                        class="btn btn-primary"
                        id="{{ $saveButtonId }}"
                    >
                        {{ $saveText }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
