<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
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

Route::post('/users-management/create_role', [UserManagementController::class, 'createRole'])->name('roles.store');

Route::put('/users-management/{id}/update_role', [UserManagementController::class, 'updateRole'])->name('roles.update');


Route::delete('/users-management/{id}/delete_role', [UserManagementController::class, 'deleteRole'])->name('roles.detele');

Route::post('/users-management/create_permission', [UserManagementController::class, 'createPermission'])->name('permissions.store');

Route::put('/users-management/{id}/update_permission', [UserManagementController::class, 'updatePermission'])->name('permissions.update');

Route::delete('/users-management/{id}/delete_permission', [UserManagementController::class, 'deletePermission'])->name('permissions.detele');

Route::get('/users-management/{user}/roles', [UserManagementController::class, 'getRoles'])
    ->name('users.roles');

Route::post('/users-management/{user}/roles', [UserManagementController::class, 'updateRolesUser'])
    ->name('users.roles.update');

//acciones sobre el estado del usuario
Route::post('/users-management/{id}/enable', [UserManagementController::class, 'enable'])->name('enable-user');

Route::post('/users-management/{id}/disable', [UserManagementController::class, 'disable'])->name('disable-user');

Route::delete('/users-management/{id}/delete', [UserManagementController::class, 'destroy'])->name('destroy-user');

// dashboard - roles
Route::get('/dashboard/roles', [DashboardController::class, 'getAllRoles'])
    ->name('roles');

Route::get('/dashboard/permissions', [DashboardController::class, 'getAllPermissions'])
    ->name('permissions');

// dashboard - Permissions
Route::get('/dashboard/{idRole}/permissions', [DashboardController::class, 'getAllPermissionsRole'])
    ->name('role-permissions');

Route::post('/settings/logos', [DashboardController::class, 'updateLogos'])
    ->name('settings.update-logos');
    
Route::get('/roles/{role}/permissions', function (Role $role) {
return $role->permissions()->select('id','name')->get();
});

Route::post('/users-management/{role}/permissions', [UserManagementController::class, 'updatePermissionsUser']);

Route::get('/users-management/{id}/info_user', [UserManagementController::class, 'getInfoUser'])->name('info-user');

Route::get('/books', [BookController::class, 'index'])->name('books');
Route::post('/books', [BookController::class, 'store'])->name('books.store');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
