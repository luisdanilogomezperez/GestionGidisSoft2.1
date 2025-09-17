<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Setting;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::orderBy('order', 'asc')->paginate(10);
        return Inertia::render('MenuItems/Index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'href' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        MenuItem::create($validated);

        // 🔹 Redirigir a la ruta que llama al index
        return redirect()->route('menu-items.index')
                        ->with('success', 'Elemento creado correctamente.');
    }

    public function destroy(string $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();

        return redirect()->route('menu-items.index')
        ->with('success', 'Item eliminado correctamente.');
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'href' => 'nullable|string|max:255',
        'icon' => 'nullable|string|max:255',
        'permission' => 'nullable|string|max:255',
        'order' => 'nullable|integer',
        ]);

         $item = MenuItem::findOrFail($id);
        $item->update($validated);

        // ⚡ Responder sin redireccionar para permitir reload parcial en Inertia
        return back()->with([
        'success' => 'Elemento actualizado correctamente',
        // Indica a Inertia que debe recargar las props compartidas
        'reload_shared_props' => true
    ]);
    }
}