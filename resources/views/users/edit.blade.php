<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users / Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('users.update',  $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="" class="text-sm font-medium">First Name</label>
                            <div class="mb-3">
                                <input value="{{ old('first_name', $user->first_name) }}" name="first_name" placeholder="Enter first name" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('first_name')
                                    <p class="text-red-400 font-medium"> {{ $message }}</p>
                                @enderror
                            </div>

                            <label for="" class="text-sm font-medium">Last Name</label>
                            <div class="mb-3">
                                <input value="{{ old('last_name', $user->last_name) }}" name="last_name" placeholder="Enter last name" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('last_name')
                                    <p class="text-red-400 font-medium"> {{ $message }}</p>
                                @enderror
                            </div>

                            <label for="" class="text-sm font-medium">Email</label>
                            <div class="mb-3">
                                <input value="{{ old('email', $user->email) }}" name="email" placeholder="Enter email" class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                                @error('email')
                                    <p class="text-red-400 font-medium"> {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-4 mb-3">
                                @if($roles->isNotEmpty())
                                    @foreach($roles as $role)
                                        <div class="mt-3">
                                            
                                            <input {{ ($hasRoles->contains($role->id)) ? 'checked' : '' }} type="checkbox" class="rounded" name="role[]" id="role-{{ $role->id }}"
                                            value="{{ $role->name }}">
                                            <label for="role-{{ $role->id }}">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                @endif    
                          
                            </div>

                            
                            <button class="bg-slate-700 text-sm rounded-md px-5 py-4 text-white hover:bg-slate-600">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
