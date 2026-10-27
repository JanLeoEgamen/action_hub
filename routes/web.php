<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard/data/my-overview', [DashboardController::class, 'myOverviewData'])->name('dashboard.data.my-overview');
Route::get('/dashboard/data/project-overview', [DashboardController::class, 'projectOverviewData'])->name('dashboard.data.project-overview');
Route::get('/dashboard/data/task-overview', [DashboardController::class, 'taskOverviewData'])->name('dashboard.data.task-overview');
Route::get('/dashboard/data/organization-overview', [DashboardController::class, 'organizationOverviewData'])->name('dashboard.data.organization-overview');

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


    // Companies
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{id}/data', [CompanyController::class, 'show'])
        ->whereNumber('id')->name('companies.show');
    Route::put('/companies/{id}', [CompanyController::class, 'update'])
        ->whereNumber('id')->name('companies.update');
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])
        ->whereNumber('id')->name('companies.destroy');

    // Departments
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{id}/data', [DepartmentController::class, 'show'])
        ->whereNumber('id')->name('departments.show');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])
        ->whereNumber('id')->name('departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])
        ->whereNumber('id')->name('departments.destroy');

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}/data', [GroupController::class, 'show'])
        ->whereNumber('id')->name('groups.show');
    Route::put('/groups/{id}', [GroupController::class, 'update'])
        ->whereNumber('id')->name('groups.update');
    Route::delete('/groups/{id}', [GroupController::class, 'destroy'])
        ->whereNumber('id')->name('groups.destroy');

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

        
    // Project List
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/list', [ProjectController::class, 'list'])->name('projects.list');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    // Project Scope ticket
    Route::get('/projects/{projectId}/tickets', [TicketController::class, 'index'])
        ->whereNumber('projectId')->name('projects.tickets');
    Route::get('/projects/{projectId}/tickets/board', [TicketController::class, 'board'])
        ->whereNumber('projectId')->name('projects.tickets.board');
    Route::post('/projects/{projectId}/tickets', [TicketController::class, 'store'])
    ->whereNumber('projectId')->name('projects.tickets.store');

    // Project
    Route::get('/projects/{id}/data', [ProjectController::class, 'show'])
        ->whereNumber('id')->name('projects.show');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])
        ->whereNumber('id')->name('projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])
        ->whereNumber('id')->name('projects.destroy');

    // Tickets
    Route::get('/tickets/{id}/data', [TicketController::class, 'show'])
        ->whereNumber('id')->name('tickets.show');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])
        ->whereNumber('id')->name('tickets.update');
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])
        ->whereNumber('id')->name('tickets.update-status');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])
        ->whereNumber('id')->name('tickets.destroy');


});

require __DIR__.'/auth.php';


