<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class DashBoardController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome'
            , [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
    }

    public function dashboard()
    {
        return Inertia::render('Dashboard');
    }

    public function getAllRoles()
    {
        // Roles disponibles
        $allRoles = Role::all(['id', 'name']);

        return response()->json([
            'roles' => $allRoles,
        ]);
    }
    public function getAllPermissions()
    {
        // Roles disponibles
        $allPermissions = Permission::all(['id', 'name']);

        return response()->json([
            'permissions' => $allPermissions,
        ]);
    }
}
