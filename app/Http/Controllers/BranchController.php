<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::with('company')->paginate(10);
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('branches.form', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20',
            'empresa_id' => 'required|exists:companies,id',
        ]);

        Branch::create($validated);

        session()->flash('success', 'Sucursal creada exitosamente.');
        return redirect()->route('branches.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load('company');
        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        $companies = Company::all();
        return view('branches.form', compact('branch', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:20',
            'empresa_id' => 'required|exists:companies,id',
        ]);

        $branch->update($validated);

        session()->flash('success', 'Sucursal actualizada exitosamente.');
        return redirect()->route('branches.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        session()->flash('success', 'Sucursal eliminada exitosamente.');
        return redirect()->route('branches.index');
    }
}
