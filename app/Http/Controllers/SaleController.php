<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use App\Models\CashBoxSession;
use App\Models\CashBoxMovement;
use App\Models\DocumentSeries;
use App\Models\Presentation;
use App\Models\Promotion;
use App\Models\Inventory;
use App\Models\Client;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['client', 'user', 'cashBoxSession.cashBox'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar que el usuario tenga una sesión de caja abierta
        $user = auth()->user();
        $session = CashBoxSession::with('cashBox')
            ->where('usuario_id', $user->id)
            ->where('estado', 'abierta')
            ->first();

        if (!$session) {
            session()->flash('error', 'Debes tener una sesión de caja abierta para realizar ventas.');
            return redirect()->route('cashboxes.index');
        }

        // Cargar datos necesarios
        $clients = Client::orderBy('nombre_completo')->get();
        $presentations = Presentation::with('product.category')->orderBy('nombre')->get();
        
        // Agregar stock a cada presentación (sumando todos los registros de inventario del mismo producto)
        $inventories = Inventory::all()->groupBy('producto_id');
        $presentations = $presentations->map(function($presentation) use ($inventories) {
            $productInventories = $inventories->get($presentation->product_id);
            // Sumar todos los stocks de los registros de inventario del mismo producto
            $totalStock = $productInventories ? $productInventories->sum('stock') : 0;
            $presentation->stock = $totalStock;
            return $presentation;
        });
        
        $promotions = Promotion::where('activa', true)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->orderBy('nombre')
            ->get();
        $categories = Category::orderBy('nombre')->get();

        return view('sales.create', compact('session', 'clients', 'presentations', 'promotions', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $session = CashBoxSession::where('usuario_id', $user->id)
            ->where('estado', 'abierta')
            ->first();

        if (!$session) {
            return back()->withErrors(['error' => 'Debes tener una sesión de caja abierta para realizar ventas.']);
        }

        $validated = $request->validate([
            'tipo_comprobante' => 'required|in:factura,boleta,ticket',
            'cliente_id' => 'nullable|exists:clients,id',
            'total_gravado' => 'required|numeric|min:0',
            'total_igv' => 'required|numeric|min:0',
            'total_venta' => 'required|numeric|min:0.01',
            'detalles' => 'required|array|min:1',
            'detalles.*.tipo' => 'required|in:presentation,promotion',
            'detalles.*.vendible_id' => 'required|integer',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.subtotal' => 'required|numeric|min:0',
            'pagos' => 'required|array|min:1',
            'pagos.*.metodo_pago' => 'required|in:efectivo,tarjeta,billetera_virtual,otro',
            'pagos.*.monto_pagado' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:255',
        ]);

        // Validar que la suma de pagos sea igual al total_venta
        $sumaPagos = collect($validated['pagos'])->sum('monto_pagado');
        if (abs($sumaPagos - $validated['total_venta']) > 0.01) {
            return back()->withInput()->withErrors(['error' => 'La suma de los pagos debe ser igual al total de la venta.']);
        }

        DB::beginTransaction();
        try {
            // Obtener la sucursal del usuario para generar el correlativo
            $branch = $user->branch;
            if (!$branch) {
                throw new \Exception('El usuario no tiene una sucursal asignada.');
            }

            // Generar serie y correlativo
            $documentSeries = DocumentSeries::where('sucursal_id', $branch->id)
                ->where('tipo_comprobante', $validated['tipo_comprobante'])
                ->first();

            if (!$documentSeries) {
                throw new \Exception('No existe una serie de documentos configurada para este tipo de comprobante en tu sucursal.');
            }

            $correlativo = $documentSeries->ultimo_correlativo + 1;
            $serie = $documentSeries->serie;

            // Verificar que no exista una venta con el mismo tipo, serie y correlativo
            $exists = Sale::where('tipo_comprobante', $validated['tipo_comprobante'])
                ->where('serie', $serie)
                ->where('correlativo', $correlativo)
                ->exists();

            if ($exists) {
                throw new \Exception('Ya existe una venta con este número de comprobante. Intenta nuevamente.');
            }

            // Crear la venta
            $sale = Sale::create([
                'tipo_comprobante' => $validated['tipo_comprobante'],
                'serie' => $serie,
                'correlativo' => $correlativo,
                'total_gravado' => $validated['total_gravado'],
                'total_igv' => $validated['total_igv'],
                'total_venta' => $validated['total_venta'],
                'cliente_id' => $validated['cliente_id'] ?? null,
                'usuario_id' => $user->id,
                'sesion_caja_id' => $session->id,
                'estado' => 'registrada',
            ]);

            // Crear detalles y descontar stock
            foreach ($validated['detalles'] as $detalle) {
                // Verificar que el vendible existe
                if ($detalle['tipo'] === 'presentation') {
                    $vendible = Presentation::find($detalle['vendible_id']);
                    if (!$vendible) {
                        throw new \Exception('La presentación seleccionada no existe.');
                    }

                    // Descontar stock
                    // La cantidad a descontar es: cantidad_vendida * unidades_de_la_presentacion
                    $unidadesADescontar = $detalle['cantidad'] * $vendible->unidades;
                    
                    // Obtener todos los registros de inventario del producto, ordenados por fecha de vencimiento (FIFO)
                    $inventories = Inventory::where('producto_id', $vendible->product_id)
                        ->orderBy('fecha_vencimiento', 'asc')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    if ($inventories->isEmpty()) {
                        throw new \Exception('No hay inventario disponible para el producto: ' . ($vendible->product->nombre ?? 'N/A'));
                    }

                    // Calcular stock total disponible
                    $stockTotal = $inventories->sum('stock');
                    
                    if ($stockTotal < $unidadesADescontar) {
                        throw new \Exception('Stock insuficiente para: ' . ($vendible->product->nombre ?? 'N/A') . ' - ' . $vendible->nombre . '. Stock disponible: ' . $stockTotal . ', necesario: ' . $unidadesADescontar);
                    }

                    // Distribuir el descuento entre los registros de inventario (FIFO)
                    $restante = $unidadesADescontar;
                    foreach ($inventories as $inventory) {
                        if ($restante <= 0) {
                            break;
                        }
                        
                        if ($inventory->stock > 0) {
                            $aDescontar = min($inventory->stock, $restante);
                            $inventory->stock -= $aDescontar;
                            $inventory->save();
                            $restante -= $aDescontar;
                        }
                    }
                } elseif ($detalle['tipo'] === 'promotion') {
                    $vendible = Promotion::find($detalle['vendible_id']);
                    if (!$vendible) {
                        throw new \Exception('La promoción seleccionada no existe.');
                    }
                    // No se descuenta stock para promociones según las especificaciones
                } else {
                    throw new \Exception('Tipo de vendible no válido.');
                }

                // Crear el detalle
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'vendible_type' => $detalle['tipo'] === 'presentation' ? Presentation::class : Promotion::class,
                    'vendible_id' => $detalle['vendible_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'descuento' => $detalle['descuento'] ?? 0,
                    'subtotal' => $detalle['subtotal'],
                ]);
            }

            // Crear pagos y movimientos de caja
            foreach ($validated['pagos'] as $pago) {
                // Crear movimiento de caja
                $movement = CashBoxMovement::create([
                    'sesion_caja_id' => $session->id,
                    'tipo' => 'ingreso',
                    'monto' => $pago['monto_pagado'],
                    'metodo_pago' => $pago['metodo_pago'],
                    'descripcion' => 'Venta ' . strtoupper($validated['tipo_comprobante']) . ' ' . $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT),
                    'origen_type' => Sale::class,
                    'origen_id' => $sale->id,
                ]);

                // Crear el pago con referencia al movimiento
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'metodo_pago' => $pago['metodo_pago'],
                    'monto_pagado' => $pago['monto_pagado'],
                    'referencia' => $pago['referencia'] ?? null,
                    'cash_box_movement_id' => $movement->id,
                ]);
            }

            // Actualizar el correlativo
            $documentSeries->ultimo_correlativo = $correlativo;
            $documentSeries->save();

            DB::commit();

            session()->flash('success', 'Venta registrada exitosamente. Comprobante: ' . $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT));
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage (Anular venta).
     */
    public function destroy(Sale $sale)
    {
        if ($sale->estado === 'anulada') {
            session()->flash('error', 'Esta venta ya está anulada.');
            return redirect()->route('sales.index');
        }

        DB::beginTransaction();
        try {
            // Cambiar estado a anulada
            $sale->estado = 'anulada';
            $sale->save();

            // Reversar stock de Presentations
            foreach ($sale->details as $detail) {
                if ($detail->vendible_type === Presentation::class) {
                    $presentation = Presentation::find($detail->vendible_id);
                    if ($presentation) {
                        // Revertir: cantidad_vendida * unidades_de_la_presentacion
                        $unidadesARevertir = $detail->cantidad * $presentation->unidades;
                        
                        // Buscar un registro de inventario existente para el producto, o crear uno nuevo
                        $inventory = Inventory::where('producto_id', $presentation->product_id)
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if ($inventory) {
                            // Agregar al inventario más reciente
                            $inventory->stock += $unidadesARevertir;
                            $inventory->save();
                        } else {
                            // Si no hay inventario, crear uno nuevo
                            Inventory::create([
                                'producto_id' => $presentation->product_id,
                                'stock' => $unidadesARevertir,
                                'fecha_vencimiento' => null,
                            ]);
                        }
                    }
                }
            }

            // Eliminar movimientos de caja usando cash_box_movement_id
            foreach ($sale->payments as $payment) {
                if ($payment->cash_box_movement_id) {
                    CashBoxMovement::where('id', $payment->cash_box_movement_id)->delete();
                }
            }

            DB::commit();

            session()->flash('success', 'Venta anulada exitosamente. El stock y los movimientos de caja han sido revertidos.');
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al anular la venta: ' . $e->getMessage()]);
        }
    }
}
