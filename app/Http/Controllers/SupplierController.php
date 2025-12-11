<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('nombre_completo')->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:DNI,RUC,Pasaporte',
            'nro_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Supplier::where('tipo_documento', $request->tipo_documento)
                        ->where('nro_documento', $value)
                        ->exists();
                    if ($exists) {
                        $fail('El número de documento ya existe para este tipo de documento.');
                    }
                },
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        Supplier::create($validated);

        session()->flash('success', 'Proveedor creado exitosamente.');
        return redirect()->route('suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:DNI,RUC,Pasaporte',
            'nro_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($request, $supplier) {
                    $exists = Supplier::where('tipo_documento', $request->tipo_documento)
                        ->where('nro_documento', $value)
                        ->where('id', '!=', $supplier->id)
                        ->exists();
                    if ($exists) {
                        $fail('El número de documento ya existe para este tipo de documento.');
                    }
                },
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        $supplier->update($validated);

        session()->flash('success', 'Proveedor actualizado exitosamente.');
        return redirect()->route('suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        session()->flash('success', 'Proveedor eliminado exitosamente.');
        return redirect()->route('suppliers.index');
    }
}
