<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::with('product')->paginate(10);
        return view('inventories.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('nombre')->get();
        return view('inventories.form', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar si viene como array (múltiples registros) o como objeto único
        if ($request->has('inventories') && is_array($request->inventories)) {
            // Múltiples registros
            $validated = $request->validate([
                'inventories' => 'required|array|min:1',
                'inventories.*.producto_id' => 'required|exists:products,id',
                'inventories.*.stock' => 'required|integer|min:0',
                'inventories.*.fecha_vencimiento' => 'nullable|date',
            ]);

            $count = 0;
            foreach ($validated['inventories'] as $inventoryData) {
                Inventory::create($inventoryData);
                $count++;
            }

            session()->flash('success', $count . ' registro(s) de inventario creado(s) exitosamente.');
        } else {
            // Un solo registro (compatibilidad hacia atrás)
            $validated = $request->validate([
                'producto_id' => 'required|exists:products,id',
                'stock' => 'required|integer|min:0',
                'fecha_vencimiento' => 'nullable|date',
            ]);

            Inventory::create($validated);
            session()->flash('success', 'Inventario creado exitosamente.');
        }

        return redirect()->route('inventories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product');
        return view('inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $products = Product::orderBy('nombre')->get();
        return view('inventories.form', compact('inventory', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $inventory->update($validated);

        session()->flash('success', 'Inventario actualizado exitosamente.');
        return redirect()->route('inventories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        session()->flash('success', 'Inventario eliminado exitosamente.');
        return redirect()->route('inventories.index');
    }
}
