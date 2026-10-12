<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    // shows permssion page
    public function index(){

    }

    // shows create permission page
    public function create(){

        return view('permissions.create');
    }

    // store permission in db
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions|min:3',
        ]);
    }

    // shows edit permission page
    public function edit(){

    }

    // shows create permission page
    public function update(){

    }


    // delete permissions in db
    public function destroy(){

    }
}
