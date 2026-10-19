
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Categories') }}
            </h2>

            <a href="{{ route('task-categories.create') }}"
               class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white hover:bg-slate-600">
                Create
            </a>
        </div>
    </x-slot>

    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Categories</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($taskCategories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>{{ $category->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.datatable').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax:{
                        url: '{{ route("task-categories.index") }}'
                    },
                    columns: [
                        { data: 'name', name: 'name'},
                        { data: 'description', name: 'description'},
                        { data: 'created_at', name: 'created_at'},
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
