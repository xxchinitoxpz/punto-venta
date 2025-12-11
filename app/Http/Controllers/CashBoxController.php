<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\CashBoxSession;
use App\Models\CashBoxMovement;
use App\Models\Branch;
use Illuminate\Http\Request;

class CashBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cashBoxes = CashBox::with(['branch', 'sessions' => function($query) {
            $query->where('estado', 'abierta')->latest();
        }])->paginate(10);
        
        return view('cash_boxes.index', compact('cashBoxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('cash_boxes.form', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        CashBox::create($validated);

        session()->flash('success', 'Caja creada exitosamente.');
        return redirect()->route('cashboxes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CashBox $cashbox)
    {
        $cashbox->load(['branch', 'sessions' => function($query) {
            $query->latest()->limit(10);
        }]);
        return view('cash_boxes.show', compact('cashbox'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashBox $cashbox)
    {
        $branches = Branch::all();
        return view('cash_boxes.form', compact('cashbox', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashBox $cashbox)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        $cashbox->update($validated);

        session()->flash('success', 'Caja actualizada exitosamente.');
        return redirect()->route('cashboxes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashBox $cashbox)
    {
        // Verificar que no tenga sesiones abiertas
        $hasOpenSession = $cashbox->sessions()->where('estado', 'abierta')->exists();
        
        if ($hasOpenSession) {
            session()->flash('error', 'No se puede eliminar una caja con sesiones abiertas.');
            return redirect()->route('cashboxes.index');
        }

        $cashbox->delete();
        session()->flash('success', 'Caja eliminada exitosamente.');
        return redirect()->route('cashboxes.index');
    }

    /**
     * Show the form for opening a new session.
     */
    public function openSession(CashBox $cashbox)
    {
        // Verificar que no haya una sesión abierta
        $openSession = $cashbox->sessions()->where('estado', 'abierta')->first();
        
        if ($openSession) {
            session()->flash('error', 'Esta caja ya tiene una sesión abierta.');
            return redirect()->route('cashboxes.index');
        }

        return view('cash_boxes.session.open', compact('cashbox'));
    }

    /**
     * Store a newly opened session.
     */
    public function storeOpenSession(Request $request, CashBox $cashbox)
    {
        // Verificar que no haya una sesión abierta
        $openSession = $cashbox->sessions()->where('estado', 'abierta')->first();
        
        if ($openSession) {
            session()->flash('error', 'Esta caja ya tiene una sesión abierta.');
            return redirect()->route('cashboxes.index');
        }

        $validated = $request->validate([
            'monto_apertura_efectivo' => 'required|numeric|min:0',
        ]);

        $session = CashBoxSession::create([
            'caja_id' => $cashbox->id,
            'usuario_id' => auth()->id(),
            'fecha_hora_apertura' => now(),
            'monto_apertura_efectivo' => $validated['monto_apertura_efectivo'],
            'estado' => 'abierta',
        ]);

        session()->flash('success', 'Sesión de caja abierta exitosamente.');
        return redirect()->route('cashboxes.showSession', $session);
    }

    /**
     * Display the specified session.
     */
    public function showSession(CashBoxSession $session)
    {
        $session->load(['cashBox.branch', 'user', 'movements' => function($query) {
            $query->latest();
        }]);
        
        return view('cash_boxes.session.show', compact('session'));
    }

    /**
     * Show the form for closing a session.
     */
    public function closeSession(CashBoxSession $session)
    {
        if ($session->estado === 'cerrada') {
            session()->flash('error', 'Esta sesión ya está cerrada.');
            return redirect()->route('cashboxes.showSession', $session);
        }

        $session->load('movements');
        return view('cash_boxes.session.close', compact('session'));
    }

    /**
     * Store the closing of a session.
     */
    public function storeCloseSession(Request $request, CashBoxSession $session)
    {
        if ($session->estado === 'cerrada') {
            session()->flash('error', 'Esta sesión ya está cerrada.');
            return redirect()->route('cashboxes.showSession', $session);
        }

        $validated = $request->validate([
            'monto_cierre_efectivo_contado' => 'required|numeric|min:0',
        ]);

        $montoEsperado = $session->monto_esperado_efectivo;
        $montoContado = $validated['monto_cierre_efectivo_contado'];
        $descuadre = $montoContado - $montoEsperado;

        $session->update([
            'monto_cierre_efectivo_contado' => $montoContado,
            'fecha_hora_cierre' => now(),
            'estado' => 'cerrada',
        ]);

        if (abs($descuadre) > 0.01) {
            session()->flash('warning', "Sesión cerrada. Descuadre detectado: " . number_format($descuadre, 2) . " " . ($descuadre > 0 ? '(sobrante)' : '(faltante)'));
        } else {
            session()->flash('success', 'Sesión cerrada exitosamente. El cuadre es correcto.');
        }

        return redirect()->route('cashboxes.showSession', $session);
    }

    /**
     * Store a manual movement (adjustment).
     */
    public function storeManualMovement(Request $request, CashBoxSession $session)
    {
        if ($session->estado === 'cerrada') {
            session()->flash('error', 'No se pueden agregar movimientos a una sesión cerrada.');
            return redirect()->route('cashboxes.showSession', $session);
        }

        $validated = $request->validate([
            'tipo' => 'required|in:ingreso,salida',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,billetera_virtual',
            'descripcion' => 'required|string|max:500',
        ]);

        // Crear un modelo temporal para el origen polimórfico
        $manualAdjustment = new class {
            public function getMorphClass()
            {
                return 'ManualAdjustment';
            }
        };

        CashBoxMovement::create([
            'sesion_caja_id' => $session->id,
            'tipo' => $validated['tipo'],
            'monto' => $validated['monto'],
            'metodo_pago' => $validated['metodo_pago'],
            'descripcion' => $validated['descripcion'],
            'origen_type' => 'ManualAdjustment',
            'origen_id' => 0,
        ]);

        session()->flash('success', 'Movimiento manual registrado exitosamente.');
        return redirect()->route('cashboxes.showSession', $session);
    }
}
