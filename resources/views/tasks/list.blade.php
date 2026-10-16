
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>

            <a href="{{ route('tasks.create') }}"
               class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white">
                Create
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>

            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-left">Assignees</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Priority</th>
                        <th class="px-6 py-3 text-left">Due Date</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse ($tasks as $task)
                        <tr class="border-b">
                            <td class="px-6 py-3">{{ $task->id }}</td>
                            <td class="px-6 py-3">{{ $task->title }}</td>
                            <td class="px-6 py-3">
                                {{ $task->category?->name ?? 'Category unavailable' }}
                            </td>
                            <td class="px-6 py-3">
                                @forelse ($task->assignees as $user)
                                    <span>
                                        {{ $user->first_name ?? $user->name }}
                                        {{ $user->last_name ?? '' }}
                                    </span>@if (!$loop->last), @endif
                                @empty
                                    <span class="text-gray-500">Unassigned</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-3">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </td>
                            <td class="px-6 py-3">
                                {{ ucfirst($task->priority) }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $task->due_date?->format('d M, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-center whitespace-nowrap">
                                <a href="{{ route('tasks.edit', $task->id) }}"
                                   class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white hover:bg-slate-600">
                                    Edit
                                </a>

                                <a href="javascript:void(0)"
                                   onclick="deleteTask({{ $task->id }})"
                                   class="bg-red-600 text-sm rounded-md px-3 py-2 text-white hover:bg-red-500">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-3 text-center">
                                No tasks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="my-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script type="text/javascript">
            function deleteTask(id) {
                if (confirm("Are you sure do you want to delete this task?")) {
                    $.ajax({
                        url: '{{ route("tasks.destroy") }}',
                        type: 'delete',
                        data: { id: id },
                        dataType: 'json',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.location.href = '{{ route("tasks.index") }}';
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON?.message || 'Unable to delete task.');
                        }
                    });
                }
            }
        </script>
    </x-slot>
</x-app-layout>
