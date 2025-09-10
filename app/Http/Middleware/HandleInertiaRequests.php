<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Cargar los ítems de menú y filtrarlos por permisos
        $menuItems = MenuItem::orderBy('order')->get()->filter(function ($item) use ($request) {
            // Mostrar siempre si no tiene permiso asignado
            if (!$item->permission) {
                return true;
            }

            // Mostrar si el usuario tiene el permiso
            return $request->user()?->can($item->permission);
        })->values();

        $user = $request->user();

        // Cargar roles/permiso del usuario (nombres)
        $userRoleNames       = $user ? $user->getRoleNames()->toArray() : [];
        $userPermissionNames = $user ? $user->getPermissionsViaRoles()->pluck('name')->toArray() : [];

        // Sólo Admin/Super Admin verá el catálogo completo (cacheado)
        $isAdminish = $user && $user->hasAnyRole(['admin','Super Admin']);

        $allRoles = $isAdminish
            ? Cache::remember('all_roles', 3600, fn() => Role::with('permissions:id,name')->get(['id','name']))
            : [];

        $allPermissions = $isAdminish
            ? Cache::remember('all_permissions', 3600, fn () =>
                Permission::query()->select('id','name')->orderBy('name')->get()->toArray()
              )
            : [];

        return [
            ...parent::share($request),
            'user.roles' => $request->user() ? $request->user()->getRoleNames() : [],
            'user.permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'roles'           => $userRoleNames,
                'permissions'     => $userPermissionNames,
                'all_roles'       => $allRoles,
                'all_permissions' => $allPermissions,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'menu' => $menuItems
        ];
    }
}
