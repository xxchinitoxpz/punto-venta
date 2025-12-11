<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::with('presentations.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $presentations = Presentation::with('product')->orderBy('nombre')->get();
        
        return view('promotions.form', compact('presentations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_promocional' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.presentation_id' => 'required|exists:presentations,id',
            'presentaciones.*.cantidad_requerida' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $promotion = Promotion::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'precio_promocional' => $validated['precio_promocional'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'activa' => $request->has('activa') ? true : false,
            ]);

            // Sincronizar presentaciones con sus cantidades
            $syncData = [];
            foreach ($validated['presentaciones'] as $presentacion) {
                $syncData[$presentacion['presentation_id']] = [
                    'cantidad_requerida' => $presentacion['cantidad_requerida']
                ];
            }
            $promotion->presentations()->sync($syncData);

            DB::commit();
            
            session()->flash('success', 'Promoción creada exitosamente.');
            return redirect()->route('promotions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear la promoción: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        $promotion->load('presentations.product');
        
        return view('promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $promotion->load('presentations');
        $presentations = Presentation::with('product')->orderBy('nombre')->get();
        
        return view('promotions.form', compact('promotion', 'presentations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_promocional' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.presentation_id' => 'required|exists:presentations,id',
            'presentaciones.*.cantidad_requerida' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $promotion->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'precio_promocional' => $validated['precio_promocional'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'activa' => $request->has('activa') ? true : false,
            ]);

            // Sincronizar presentaciones con sus cantidades
            $syncData = [];
            foreach ($validated['presentaciones'] as $presentacion) {
                $syncData[$presentacion['presentation_id']] = [
                    'cantidad_requerida' => $presentacion['cantidad_requerida']
                ];
            }
            $promotion->presentations()->sync($syncData);

            DB::commit();
            
            session()->flash('success', 'Promoción actualizada exitosamente.');
            return redirect()->route('promotions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la promoción: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        try {
            $promotion->delete();
            
            session()->flash('success', 'Promoción eliminada exitosamente.');
            return redirect()->route('promotions.index');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la promoción: ' . $e->getMessage()]);
        }
    }
}
