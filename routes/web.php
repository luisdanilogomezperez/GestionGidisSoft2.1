<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/users-management', [UserManagementController::class, 'index'])->name('users');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
