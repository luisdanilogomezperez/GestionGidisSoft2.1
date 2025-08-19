<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
                ->where('id', '!=', auth()->id()) // Excluye al usuario actual
                ->paginate(25);

        return Inertia::render("PagesUsers/Index", [
            'users' => $users,
            'authUser' => auth()->user(), // Envía el usuario logueado
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("PegesUsers/Create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function getRoles(User $user)
    {
        // Roles disponibles
        $allRoles = Role::all(['id', 'name']);
        // Roles asignados al usuario
        $userRoles = $user->roles()->pluck('id');

        return response()->json([
            'roles' => $allRoles,
            'userRoles' => $userRoles,
        ]);
    }

    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return back()->with('success', 'Roles actualizados correctamente.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        // Validar que vienen permisos como array
        $data = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id'
        ]);

        // Sincronizar permisos con el rol
        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente.',
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function enable(string $id)
    {
        //
    }

    public function disable(string $id)
    {
        //
    }
}
