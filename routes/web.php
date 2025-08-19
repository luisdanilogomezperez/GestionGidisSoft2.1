<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MenuItemController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/users-management', [UserManagementController::class, 'index'])->name('users');

Route::get('/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');

Route::post('/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');

Route::put('/menu-items/{id}', [MenuItemController::class, 'update'])->name('menu-items.update');

Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');

// Users Management - Roles
Route::get('/users-management/{user}/roles', [UserManagementController::class, 'getRoles'])
    ->name('users.roles');

Route::post('/users-management/{user}/roles', [UserManagementController::class, 'updateRoles'])
    ->name('users.roles.update');

// dashboard - roles
Route::get('/dashboard/roles', [DashboardController::class, 'getAllRoles'])
    ->name('roles');

// dashboard - Permissions
Route::get('/dashboard/{idRole}/permissions', [DashboardController::class, 'getAllPermissionsRole'])
    ->name('role-permissions');

Route::get('/roles/{role}/permissions', function (Role $role) {
return $role->permissions()->select('id','name')->get();
});

Route::post('/users-management/{role}/permissions', [UserManagementController::class, 'updatePermissions']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
