
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks / Create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tasks.store') }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="text-sm font-medium">Title</label>
                            <div class="mb-3">
                                <input
                                    value="{{ old('title') }}"
                                    name="title"
                                    id="title"
                                    placeholder="Enter task title"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg"
                                    required
                                >
                                @error('title')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="text-sm font-medium">Description</label>
                            <div class="mb-3">
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="4"
                                    placeholder="Enter task description"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="text-sm font-medium">Category</label>
                            <div class="mb-3">
                                <select name="category_id" id="category_id"
                                        class="border-gray-300 shadow-sm w-1/2 rounded-lg" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="assignees" class="text-sm font-medium">Assign Users</label>
                            <p class="text-sm text-gray-500 mb-1">Hold Ctrl (Windows) or Command (Mac) to select multiple users.</p>
                            <div class="mb-3">
                                <select name="assignees[]" id="assignees" multiple
                                        class="border-gray-300 shadow-sm w-1/2 rounded-lg" size="5">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, old('assignees', [])) ? 'selected' : '' }}>
                                            {{ $user->first_name ?? $user->name }}
                                            {{ $user->last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assignees')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                                @error('assignees.*')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="text-sm font-medium">Status</label>
                            <div class="mb-3">
                                <select name="status" id="status"
                                        class="border-gray-300 shadow-sm w-1/2 rounded-lg" required>
                                    @foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status', 'pending') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="text-sm font-medium">Priority</label>
                            <div class="mb-3">
                                <select name="priority" id="priority"
                                        class="border-gray-300 shadow-sm w-1/2 rounded-lg" required>
                                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('priority', 'medium') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="text-sm font-medium">Due Date</label>
                            <div class="mb-3">
                                <input type="date" name="due_date" id="due_date"
                                       value="{{ old('due_date') }}"
                                       class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('due_date')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="text-sm font-medium">Status</label>
                            <div class="mb-3">
                                <select name="is_active" id="is_active"
                                        class="border-gray-300 shadow-sm w-1/2 rounded-lg" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white hover:bg-slate-600">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
