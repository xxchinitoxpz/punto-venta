<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::orderBy('nombre_completo')->paginate(10);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:DNI,RUC',
            'nro_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Client::where('tipo_documento', $request->tipo_documento)
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

        Client::create($validated);

        session()->flash('success', 'Cliente creado exitosamente.');
        return redirect()->route('clients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:DNI,RUC',
            'nro_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($request, $client) {
                    $exists = Client::where('tipo_documento', $request->tipo_documento)
                        ->where('nro_documento', $value)
                        ->where('id', '!=', $client->id)
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

        $client->update($validated);

        session()->flash('success', 'Cliente actualizado exitosamente.');
        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        session()->flash('success', 'Cliente eliminado exitosamente.');
        return redirect()->route('clients.index');
    }
}
