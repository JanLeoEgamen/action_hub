<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return[
            new Middleware('permission:view task category', only: ['index']),
            new Middleware('permission:create task category', only: ['create']),
            new Middleware('permission:edit task category', only: ['edit']),
            new Middleware('permission:delete task category', only: ['destroy'])
        ];
    }

    public function index(){
        $roles = Role::orderBy('name', 'ASC')->paginate(10);
        return view('roles.index', [
            'roles' => $roles
        ]);
    }

    public function create(){
        $permissions = Permission::orderBy('name', 'DESC')->get();

        return view('roles.create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request){

      $validated  = $request->validate([
            'name' => 'required|between:4,30' 
        ], [
            'name.required' => 'Please enter the role name',
            'name.between' => 'Please enter characters between 4 to 20'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if(!empty($request->permission)){
            foreach($request->permission as $name){
                $role->givePermissionTo($name);
            }
        }
        return redirect()->route('roles.index')->with('success', 'Role Added Successfully');
    }

    public function edit(Role $role){
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name', 'DESC')->get();
        return view('roles.edit', compact('permissions', 'hasPermissions', 'role'));
    }

    public function update(Request $request, Role $role){

      $validated  = $request->validate([
        'name' => 'required|between:4,30' 
        ], [
            ' name.required' => 'Please enter the permission name',
            ' name.between' => 'Please enter characters between 4 to 20',
        ]);

        $role->update($validated);

        if(!empty($request->permission)){
            $role->syncPermissions($request->permission);
        }else{
            $role->syncPermissions([]);
        }
        return redirect()->route('roles.index')->with('success', 'Role Updated Successfully');

    }

    public function destroy(Role $role){
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
