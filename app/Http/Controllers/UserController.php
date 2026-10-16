<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return[
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:create users', only: ['create']),
            new Middleware('permission:edit users', only: ['edit']),
            new Middleware('permission:delete users', only: ['destroy'])
        ];
    }

    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index',[
            'users' => $users
        ]);
    }


    public function create()
    {
        return view('users.create');
    }


    public function store(Request $request)
    {
        
    }


    public function show(string $id)
    {
        //
    }

    public function edit(User $user)
    {   
        $roles = Role::orderBy('name', 'ASC')->get();
        $hasRoles =$user->roles->pluck('id');
        return view('users.edit', compact('user', 'roles', 'hasRoles'));
    }


    public function update(Request $request, User $user)
    {
        $validated  = $request->validate([
        'first_name' => 'required',
        'last_name' => 'required|between:4,30',
        'email' => 'required|email' 
        ], [
            'first_name.required' => 'Please enter the permission name',
            'last_name.between' => 'Please enter characters between 4 to 20',
            'email.between' => 'Please enter characters between 4 to 20',
            'email.email' => 'Please enter you email',
        ]);

        $user->update($validated);

        $user->syncRoles($request->role);


        return redirect()->route('users.index')->with('success', 'User Updated Successfully');

        
    }


    public function destroy(string $id)
    {
        //
    }
}
