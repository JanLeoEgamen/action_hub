<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/data', [UserController::class, 'show'])
        ->whereNumber('id')->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])
        ->whereNumber('id')->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->whereNumber('id')->name('users.destroy');
    
    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/data', [RoleController::class, 'show'])
        ->whereNumber('id')->name('roles.show');
    Route::put('/roles/{id}', [RoleController::class, 'update'])
        ->whereNumber('id')->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->whereNumber('id')->name('roles.destroy');

    // TaskCategory
    Route::get('/task-categories', [TaskCategoryController::class, 'index'])->name('task-categories.index');
    Route::post('/task-categories', [TaskCategoryController::class, 'store'])->name('task-categories.store');
    Route::get('/task-categories/{id}/data', [TaskCategoryController::class, 'show'])
        ->whereNumber('id')->name('task-categories.show');
    Route::put('/task-categories/{id}', [TaskCategoryController::class, 'update'])
        ->whereNumber('id')->name('task-categories.update');
    Route::delete('/task-categories/{id}', [TaskCategoryController::class, 'destroy'])
        ->whereNumber('id')->name('task-categories.destroy');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/board', [TaskController::class, 'board'])->name('tasks.board');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}/data', [TaskController::class, 'show'])
        ->whereNumber('id')->name('tasks.show');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])
        ->whereNumber('id')->name('tasks.update');
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])
        ->whereNumber('id')->name('tasks.update-status');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])
        ->whereNumber('id')->name('tasks.destroy');



});

require __DIR__.'/auth.php';
