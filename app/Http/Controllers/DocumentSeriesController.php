<?php

namespace App\Http\Controllers;

use App\Models\DocumentSeries;
use App\Models\Branch;
use Illuminate\Http\Request;

class DocumentSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentSeries = DocumentSeries::with('branch.company')->paginate(10);
        return view('document-series.index', compact('documentSeries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::with('company')->get();
        return view('document-series.form', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_comprobante' => 'required|string|max:255',
            'serie' => 'required|string|max:20',
            'ultimo_correlativo' => 'required|integer|min:0',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        DocumentSeries::create($validated);

        session()->flash('success', 'Serie de documento creada exitosamente.');
        return redirect()->route('document-series.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentSeries $documentSeries)
    {
        $documentSeries->load('branch.company');
        return view('document-series.show', compact('documentSeries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentSeries $documentSeries)
    {
        $branches = Branch::with('company')->get();
        return view('document-series.form', compact('documentSeries', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentSeries $documentSeries)
    {
        $validated = $request->validate([
            'tipo_comprobante' => 'required|string|max:255',
            'serie' => 'required|string|max:20',
            'ultimo_correlativo' => 'required|integer|min:0',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        $documentSeries->update($validated);

        session()->flash('success', 'Serie de documento actualizada exitosamente.');
        return redirect()->route('document-series.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentSeries $documentSeries)
    {
        $documentSeries->delete();
        session()->flash('success', 'Serie de documento eliminada exitosamente.');
        return redirect()->route('document-series.index');
    }
}
