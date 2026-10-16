
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Categories / Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('task-categories.update', $taskCategory->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="text-sm font-medium">
                                Name
                            </label>

                            <div class="mb-3">
                                <input
                                    value="{{ old('name', $taskCategory->name) }}"
                                    name="name"
                                    id="name"
                                    placeholder="Enter task category name"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg"
                                >

                                @error('name')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="text-sm font-medium">
                                Description
                            </label>

                            <div class="mb-3">
                                <textarea
                                    name="description"
                                    id="description"
                                    placeholder="Enter description"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg"
                                    rows="4"
                                >{{ old('description', $taskCategory->description) }}</textarea>

                                @error('description')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="text-sm font-medium">
                                Status
                            </label>

                            <div class="mb-3">
                                <select
                                    name="is_active"
                                    id="is_active"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg"
                                >
                                    <option value="1" {{ (string) old('is_active', (int) $taskCategory->is_active) === '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ (string) old('is_active', (int) $taskCategory->is_active) === '0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>

                                @error('is_active')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white hover:bg-slate-600">
                            Update
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
