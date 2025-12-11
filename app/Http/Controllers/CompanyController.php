<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'required|string|max:255',
            'cert_path' => 'required|file|max:5120',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'boolean',
        ]);

        // Manejar logo
        if ($request->hasFile('logo_path')) {
            $logoPath = $request->file('logo_path')->store('companies/logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // Manejar certificado
        if ($request->hasFile('cert_path')) {
            $certPath = $request->file('cert_path')->store('companies/certs', 'public');
            $validated['cert_path'] = $certPath;
        }

        $validated['production'] = $request->has('production');

        Company::create($validated);

        session()->flash('success', 'Empresa creada exitosamente.');
        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.form', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'nullable|string|max:255',
            'cert_path' => 'nullable|file|max:5120',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'boolean',
        ]);

        // Manejar logo
        if ($request->hasFile('logo_path')) {
            // Eliminar logo anterior si existe
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $logoPath = $request->file('logo_path')->store('companies/logos', 'public');
            $validated['logo_path'] = $logoPath;
        } else {
            // Mantener el logo existente si no se sube uno nuevo
            unset($validated['logo_path']);
        }

        // Manejar certificado
        if ($request->hasFile('cert_path')) {
            // Eliminar certificado anterior si existe
            if ($company->cert_path) {
                Storage::disk('public')->delete($company->cert_path);
            }
            $certPath = $request->file('cert_path')->store('companies/certs', 'public');
            $validated['cert_path'] = $certPath;
        } else {
            // Mantener el certificado existente si no se sube uno nuevo
            unset($validated['cert_path']);
        }

        // Mantener sol_pass si no se proporciona uno nuevo
        if (empty($validated['sol_pass'])) {
            unset($validated['sol_pass']);
        }

        $validated['production'] = $request->has('production');

        $company->update($validated);

        session()->flash('success', 'Empresa actualizada exitosamente.');
        return redirect()->route('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Eliminar archivos asociados
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }
        if ($company->cert_path) {
            Storage::disk('public')->delete($company->cert_path);
        }

        $company->delete();
        session()->flash('success', 'Empresa eliminada exitosamente.');
        return redirect()->route('companies.index');
    }
}
