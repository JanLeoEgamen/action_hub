<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // shows permssion page
    public function index(){
    $permissions = Permission::orderBy('created_at')->paginate(10);
        return view('permissions.list', [
            'permissions' => $permissions
        ]);
    }

    // shows create permission page
    public function create(){

        return view('permissions.create');
    }

    // store permission in db
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions|min:3'
        ]);

        if($validator->passes()){
            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.index')->with('success', 'Permission Added Successfully');
        }else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    // shows edit permission page
    public function edit($id){
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', [
            'permission' => $permission
        ]);
    }

    // shows create permission page
    public function update($id, Request $request){
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions,name,'.$id.',id'
        ]);

        if($validator->passes()){
            $permission->name = $request->name;
            $permission->save();
            return redirect()->route('permissions.index')->with('success', 'Permission Updated Successfully');
        }else{
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }

    }


    // delete permissions in db

    public function destroy(Request $request){
        $id = $request->id;

        $permission = Permission::find($id);

        if ($permission == null) {
            session()->flash('error', 'Permission not found');
            return response()->json([
                'status' => false
            ]);
        }

        $permission->delete();

        session()->flash('success', 'Permission Deleted Successfully');

        return response()->json([
            'status' => true,
        ]);
    }

}
