@props([
    'title',
    'createText' => 'Create',
    'createId' => 'createRecord',
])

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $title }}</h4>

    <button
        type="button"
        class="btn btn-primary"
        id="{{ $createId }}"
    >
        {{ $createText }}
    </button>
</div>
