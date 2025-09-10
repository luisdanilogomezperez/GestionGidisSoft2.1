<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use \Illuminate\Validation\ValidationException;

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

    public function createRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:roles,name',
            ]);

            $role = Role::create(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado correctamente',
                'role' => $role,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Rol actualizado correctamente.',
            'role' => $role,
        ]);
    }

    public function deleteRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado correctamente.',
        ]);
    }

    public function deletePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado correctamente.',
        ]);
    }

    public function createPermission(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name',
            ]);

            $permission = Permission::create(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado correctamente',
                'permission' => $permission,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Permiso actualizado correctamente.',
            'permission' => $permission,
        ]);
    }

    public function updateRolesUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return back()->with('success', 'Roles actualizados correctamente.');
    }

    public function updatePermissionsUser(Request $request, Role $role)
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
    public function show(User $user)
    {
        // Cargamos todas las relaciones del usuario
    }

    public function getInfoUser(string $id)
    {
        $userInfo = User::with([
            'roles',
            'articles',
            'books',
            'bookChapters',
            'directedProjects',
            'events',
            'otherJobs',
            'presentations',
            'researchProjects'
        ])->findOrFail($id);

        return Inertia::render('PagesUsers/UserInfo', ['userInfo' => $userInfo]);
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
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurió un error al eliminar el usuario.']);
        }
    }

    public function enable(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_enable = true;
            $user->save();

            return response()->json(['message' => 'Usuario habilitado correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'No se pudo habilitar el usuario.']);
        }
        
    }

    public function disable(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->is_enable = false;
            $user->save();

            return response()->json(['message' => 'Usuario deshabilitado correctamente']);
            //return back()->with('success', 'Usuario deshabilitado correctamente.');
        } catch (\Throwable $th) {
            return response()->json(['error' => 'No se pudo deshabilitar el usuario.']);
            //return back()->with('error', 'No se pudo deshabilitar el usuario.');
        }
        
    }
}
