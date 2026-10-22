@props([
    'title',
    'actionLabel' => null,
    'actionId' => null,
    'permission' => null,
])

<div class="flex justify-between items-center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __($title) }}
    </h2>

    @if($actionLabel && $actionId)
        @can($permission)
            <button
                type="button"
                id="{{ $actionId }}"
                class="bg-slate-700 text-sm rounded-md
                       px-5 py-3 text-white hover:bg-slate-600"
            >
                {{ $actionLabel }}
            </button>
        @endcan
    @endif
</div>
