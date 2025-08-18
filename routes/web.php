<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MenuItemController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/users-management', [UserManagementController::class, 'index'])->name('users');

Route::get('/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');

Route::post('/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');

Route::put('/menu-items/{id}', [MenuItemController::class, 'update'])->name('menu-items.update');

Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
