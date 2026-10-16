<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(){
    $permissions = Permission::orderBy('created_at')->paginate(5);
        return view('permissions.index', [
            'permissions' => $permissions
        ]);
    }

    public function create(){

        return view('permissions.create');
    }

    public function store(Request $request){

        $validated  = $request->validate([
        'name' => 'required|between:4,30' 
        ], [
            ' name.required' => 'Please enter the permission name',
            ' name.between' => 'Please enter characters between 4 to 20',
        ]);

        Permission::create([
            'name' => $request->name,
        ]);
        
        return redirect()->route('permissions.index')->with('success', 'Permission added successfully');

    }

    public function edit(Permission $permission){
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission){
        $validated  = $request->validate([
        'name' => 'required|between:4,30' 
        ], [
            ' name.required' => 'Please enter the permission name',
            ' name.between' => 'Please enter characters between 4 to 20',
        ]);

        $permission->update($validated);
        
        return redirect()->route('permissions.index')->with('success', 'Permission udpated successfully');
    }


    public function destroy(Permission $permission){
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }

}
