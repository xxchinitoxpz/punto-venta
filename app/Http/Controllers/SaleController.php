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
use App\Models\SunatResponse;
use App\Services\ApiPeruService;
use App\Services\SunatService;
use App\Traits\SunatTrait;
use Greenter\Report\XmlUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    use SunatTrait;
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
            'numero_documento' => 'nullable|string|max:11',
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

        // Validar que la suma de pagos sea mayor o igual al total_venta
        $sumaPagos = collect($validated['pagos'])->sum('monto_pagado');
        if ($sumaPagos < $validated['total_venta'] - 0.01) {
            return back()->withInput()->withErrors(['error' => 'La suma de los pagos debe ser mayor o igual al total de la venta.']);
        }

        // Calcular vuelto si hay sobrepago
        $vuelto = max(0, $sumaPagos - $validated['total_venta']);

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

            // Manejar cliente según tipo de comprobante
            $clienteId = null;
            if ($validated['tipo_comprobante'] === 'factura') {
                // Facturas siempre requieren cliente con RUC
                if (isset($validated['numero_documento']) && !empty($validated['numero_documento'])) {
                    $cliente = Client::where('tipo_documento', 'RUC')
                        ->where('nro_documento', $validated['numero_documento'])
                        ->first();
                    
                    if (!$cliente) {
                        // Si no existe, intentar consultar la API y crear el cliente
                        $apiService = new ApiPeruService();
                        $datosApi = $apiService->consultarRuc($validated['numero_documento']);
                        if ($datosApi) {
                            $cliente = Client::create([
                                'nombre_completo' => $datosApi['nombre_o_razon_social'] ?? '',
                                'tipo_documento' => 'RUC',
                                'nro_documento' => $datosApi['ruc'] ?? $validated['numero_documento'],
                                'telefono' => null,
                                'email' => null,
                                'direccion' => $datosApi['direccion_completa'] ?? ($datosApi['direccion'] ?? null),
                            ]);
                        }
                    }
                    
                    if ($cliente) {
                        $clienteId = $cliente->id;
                    } else {
                        throw new \Exception('No se pudo obtener información del cliente con RUC: ' . $validated['numero_documento']);
                    }
                } else {
                    throw new \Exception('Las facturas requieren un número de RUC del cliente.');
                }
            } elseif ($validated['tipo_comprobante'] === 'boleta') {
                // Boletas pueden tener cliente con DNI o ser boleta simple (sin cliente)
                if (isset($validated['numero_documento']) && !empty($validated['numero_documento'])) {
                    // Buscar cliente por DNI
                    $cliente = Client::where('tipo_documento', 'DNI')
                        ->where('nro_documento', $validated['numero_documento'])
                        ->first();
                    
                    if (!$cliente) {
                        // Si no existe, intentar consultar la API y crear el cliente
                        $apiService = new ApiPeruService();
                        $datosApi = $apiService->consultarDni($validated['numero_documento']);
                        if ($datosApi) {
                            $cliente = Client::create([
                                'nombre_completo' => $datosApi['nombre_completo'] ?? '',
                                'tipo_documento' => 'DNI',
                                'nro_documento' => $datosApi['numero'] ?? $validated['numero_documento'],
                                'telefono' => null,
                                'email' => null,
                                'direccion' => null,
                            ]);
                        }
                    }
                    
                    if ($cliente) {
                        $clienteId = $cliente->id;
                    }
                    // Si no se encuentra cliente, $clienteId queda null (boleta simple)
                }
                // Si no se proporciona número de documento, $clienteId queda null (boleta simple)
            } else {
                // Para ticket, usar cliente_id si viene, sino null (cliente genérico)
                $clienteId = $validated['cliente_id'] ?? null;
            }

            // Crear la venta
            $sale = Sale::create([
                'tipo_comprobante' => $validated['tipo_comprobante'],
                'serie' => $serie,
                'correlativo' => $correlativo,
                'total_gravado' => $validated['total_gravado'],
                'total_igv' => $validated['total_igv'],
                'total_venta' => $validated['total_venta'],
                'cliente_id' => $clienteId,
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
            $totalAsignado = 0;
            $vueltoTotal = $vuelto;
            
            foreach ($validated['pagos'] as $index => $pago) {
                // Calcular cuánto asignar de este pago al total de la venta
                $restantePorAsignar = $validated['total_venta'] - $totalAsignado;
                $montoAAsignar = min($pago['monto_pagado'], $restantePorAsignar);
                
                // Calcular vuelto de este pago si es en efectivo y hay sobrepago
                $vueltoDeEstePago = 0;
                if ($pago['metodo_pago'] === 'efectivo' && $vueltoTotal > 0) {
                    // Si este pago tiene sobrepago, calcular el vuelto
                    $sobrepagoDeEstePago = $pago['monto_pagado'] - $montoAAsignar;
                    if ($sobrepagoDeEstePago > 0) {
                        // El vuelto es el mínimo entre el sobrepago de este pago y el vuelto total restante
                        $vueltoDeEstePago = min($sobrepagoDeEstePago, $vueltoTotal);
                        $vueltoTotal -= $vueltoDeEstePago;
                    }
                }
                
                // Crear movimiento de caja (solo se registra el monto asignado al total, no el sobrepago)
                $descripcion = 'Venta ' . strtoupper($validated['tipo_comprobante']) . ' ' . $serie . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT);
                if ($vueltoDeEstePago > 0) {
                    $descripcion .= ' (Pago: S/ ' . number_format($pago['monto_pagado'], 2) . ', Vuelto: S/ ' . number_format($vueltoDeEstePago, 2) . ')';
                } elseif ($vueltoTotal > 0 && $pago['metodo_pago'] === 'efectivo' && $index === count($validated['pagos']) - 1) {
                    // Si hay vuelto restante y este es el último pago en efectivo, agregarlo aquí
                    $descripcion .= ' (Pago: S/ ' . number_format($pago['monto_pagado'], 2) . ', Vuelto: S/ ' . number_format($vueltoTotal, 2) . ')';
                    $vueltoTotal = 0;
                }
                
                $movement = CashBoxMovement::create([
                    'sesion_caja_id' => $session->id,
                    'tipo' => 'ingreso',
                    'monto' => $montoAAsignar, // Solo el monto que corresponde al total
                    'metodo_pago' => $pago['metodo_pago'],
                    'descripcion' => $descripcion,
                    'origen_type' => Sale::class,
                    'origen_id' => $sale->id,
                ]);

                // Crear el pago con referencia al movimiento
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'metodo_pago' => $pago['metodo_pago'],
                    'monto_pagado' => $pago['monto_pagado'], // Se guarda el monto pagado completo para referencia
                    'referencia' => $pago['referencia'] ?? null,
                    'cash_box_movement_id' => $movement->id,
                ]);
                
                $totalAsignado += $montoAAsignar;
            }

            // Actualizar el correlativo
            $documentSeries->ultimo_correlativo = $correlativo;
            $documentSeries->save();

            DB::commit();

            // Si es factura o boleta, enviar a SUNAT
            if (in_array($validated['tipo_comprobante'], ['factura', 'boleta'])) {
                try {
                    $this->enviarASunat($sale);
                } catch (\Exception $e) {
                    // Log el error pero no interrumpir el flujo
                    \Log::error('Error al enviar a SUNAT: ' . $e->getMessage());
                    session()->flash('warning', 'Venta registrada pero hubo un error al enviar a SUNAT: ' . $e->getMessage());
                }
            }

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

        // Validar que la venta tenga máximo 3 días de antigüedad
        $diasTranscurridos = $sale->created_at->diffInDays(now());
        if ($diasTranscurridos > 3) {
            session()->flash('error', 'No se puede anular una venta con más de 3 días de antigüedad. Días transcurridos: ' . $diasTranscurridos);
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

    /**
     * Consultar DNI o RUC desde la API
     */
    public function consultarDocumento(Request $request)
    {
        try {
            $request->validate([
                'tipo' => 'required|in:dni,ruc',
                'numero' => 'required|string|max:11',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos. Verifique el tipo y número de documento.'
            ], 422);
        }

        $apiService = new ApiPeruService();
        $datos = null;

        try {
            if ($request->tipo === 'dni') {
                $datos = $apiService->consultarDni($request->numero);
            } else {
                $datos = $apiService->consultarRuc($request->numero);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar la API. Intente nuevamente.'
            ], 500);
        }

        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo consultar el documento. Verifique el número ingresado.'
            ], 200);
        }

        // Verificar si el cliente ya existe en la base de datos
        $tipoDocumento = $request->tipo === 'dni' ? 'DNI' : 'RUC';
        $numeroDocumento = $request->tipo === 'dni' 
            ? ($datos['numero'] ?? $request->numero)
            : ($datos['ruc'] ?? $request->numero);
        
        $cliente = Client::where('tipo_documento', $tipoDocumento)
            ->where('nro_documento', $numeroDocumento)
            ->first();

        // Preparar respuesta según tipo
        if ($request->tipo === 'dni') {
            $response = [
                'success' => true,
                'data' => $datos,
                'cliente_existe' => $cliente !== null,
                'cliente_id' => $cliente ? $cliente->id : null,
                'cliente_nombre' => $cliente ? $cliente->nombre_completo : ($datos['nombre_completo'] ?? ''),
                'numero_documento' => $datos['numero'] ?? $request->numero,
            ];
        } else {
            $response = [
                'success' => true,
                'data' => $datos,
                'cliente_existe' => $cliente !== null,
                'cliente_id' => $cliente ? $cliente->id : null,
                'cliente_nombre' => $cliente ? $cliente->nombre_completo : ($datos['nombre_o_razon_social'] ?? ''),
                'numero_documento' => $datos['ruc'] ?? $request->numero,
                'direccion' => $datos['direccion_completa'] ?? ($datos['direccion'] ?? ''),
            ];
        }

        return response()->json($response);
    }

    /**
     * Transformar Sale a formato Greenter y enviar a SUNAT
     */
    private function enviarASunat(Sale $sale)
    {
        // Cargar relaciones necesarias
        $sale->load([
            'client',
            'details',
            'user.branch.company'
        ]);

        // Cargar relaciones de los vendibles (presentations con unitSunat y product)
        foreach ($sale->details as $detail) {
            if ($detail->vendible_type === Presentation::class) {
                $detail->load(['vendible.unitSunat', 'vendible.product']);
            }
        }

        // Obtener la empresa desde la sucursal del usuario
        $company = $sale->user->branch->company;
        if (!$company) {
            throw new \Exception('No se encontró la empresa asociada a la sucursal.');
        }

        // Validar que la empresa tenga los datos necesarios
        if (!$company->ruc || !$company->cert_path) {
            throw new \Exception('La empresa no tiene configurado el RUC o certificado digital.');
        }

        // Validar cliente: facturas siempre requieren cliente, boletas pueden ser simples
        if ($sale->tipo_comprobante === 'factura' && !$sale->client) {
            throw new \Exception('Las facturas deben tener un cliente asociado con RUC.');
        }

        // Transformar Sale a formato Greenter
        $data = $this->saleToGreenterFormat($sale, $company);

        // Calcular totales y leyendas
        $this->setTotales($data);
        $this->setLegends($data);

        // Enviar a SUNAT
        $sunatService = new SunatService();
        $see = $sunatService->getSee($company);
        $invoice = $sunatService->getInvoice($data);

        $result = $see->send($invoice);

        // Obtener XML antes de procesar respuesta
        $xml = $see->getFactory()->getLastXml();
        $xmlUtils = new XmlUtils();
        $hash = $xmlUtils->getHashSign($xml);

        // Procesar respuesta
        $response = $sunatService->sunatResponse($result);
        
        // Agregar XML y hash a la respuesta
        $response['xml'] = $xml;
        $response['hash'] = $hash;

        // Guardar respuesta en la base de datos
        $this->guardarRespuestaSunat($sale, $response, $see);

        return $response;
    }

    /**
     * Generar PDF de la venta (factura/boleta)
     */
    public function pdf(Sale $sale)
    {
        // Solo permitir PDF para facturas y boletas
        if (!in_array($sale->tipo_comprobante, ['factura', 'boleta'])) {
            return back()->with('error', 'Solo se pueden generar PDFs para facturas y boletas.');
        }

        try {
            // Cargar relaciones necesarias
            $sale->load([
                'client',
                'details',
                'user.branch.company',
                'sunatResponse'
            ]);

            // Cargar relaciones de los vendibles
            foreach ($sale->details as $detail) {
                if ($detail->vendible_type === Presentation::class) {
                    $detail->load(['vendible.unitSunat', 'vendible.product']);
                }
            }

            // Obtener la empresa
            $company = $sale->user->branch->company;
            if (!$company) {
                return back()->with('error', 'No se encontró la empresa asociada.');
            }

            // Transformar Sale a formato Greenter
            $data = $this->saleToGreenterFormat($sale, $company);

            // Calcular totales y leyendas
            $this->setTotales($data);
            $this->setLegends($data);

            // Generar invoice de Greenter
            $sunatService = new SunatService();
            $invoice = $sunatService->getInvoice($data);

            // Generar PDF
            $htmlReport = new \Greenter\Report\HtmlReport();
            $resolver = new \Greenter\Report\Resolver\DefaultTemplateResolver();
            $htmlReport->setTemplate($resolver->getTemplate($invoice));

            $report = new \Greenter\Report\PdfReport($htmlReport);
            $report->setOptions([
                'no-outline',
                'viewport-size' => '1280x1024',
                'page-width' => '21cm',
                'page-height' => '29.7cm',
            ]);
            $report->setBinPath(env('WKHTMLTOPDF_PATH'));

            // Obtener hash del XML si existe respuesta de SUNAT
            $hash = 'qqnr2dN4p/HmaEA/CJuVGo7dv5g='; // Default
            if ($sale->sunatResponse && $sale->sunatResponse->count() > 0) {
                $latestResponse = $sale->sunatResponse->sortByDesc('created_at')->first();
                if ($latestResponse && !empty($latestResponse->hash)) {
                    $hash = (string) $latestResponse->hash;
                }
            }

            // Obtener logo si existe - usar el mismo método que SunatService
            $logo = null;
            if (!empty($company->logo_path)) {
                try {
                    // Intentar obtener el logo desde el disco público
                    if (Storage::disk('public')->exists($company->logo_path)) {
                        $logo = Storage::disk('public')->get($company->logo_path);
                    } elseif (Storage::exists($company->logo_path)) {
                        // Fallback: intentar desde el disco por defecto
                        $logo = Storage::get($company->logo_path);
                    }
                } catch (\Exception $e) {
                    // Si hay error al obtener el logo, dejarlo null
                    $logo = null;
                }
            }

            // Asegurar que hash sea string válido
            $hash = (string) $hash;
            if (empty($hash)) {
                $hash = 'qqnr2dN4p/HmaEA/CJuVGo7dv5g=';
            }

            $params = [
                'system' => [
                    'logo' => $logo, // Puede ser null si no existe logo
                    'hash' => $hash,
                ],
                'user' => [
                    'header' => !empty($company->telefono) ? 'Telf: <b>' . htmlspecialchars((string)$company->telefono) . '</b>' : '',
                    'extras' => [
                        ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                        ['name' => 'VENDEDOR', 'value' => htmlspecialchars((string)($sale->user->name ?? 'N/A'))],
                    ],
                    'footer' => !empty($company->resolucion) ? '<p>Nro Resolucion: <b>' . htmlspecialchars((string)$company->resolucion) . '</b></p>' : ''
                ]
            ];

            $pdf = $report->render($invoice, $params);

            // Nombre del archivo
            $nombreArchivo = strtoupper($sale->tipo_comprobante) . '-' . $sale->serie . '-' . str_pad($sale->correlativo, 8, '0', STR_PAD_LEFT) . '.pdf';

            return response($pdf, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $nombreArchivo . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Transformar Sale a formato JSON para Greenter
     */
    private function saleToGreenterFormat(Sale $sale, $company): array
    {
        // Mapear tipo de comprobante
        $tipoDocMap = [
            'factura' => '01',
            'boleta' => '03',
        ];

        // Mapear tipo de documento del cliente
        $tipoDocClienteMap = [
            'DNI' => '1',
            'RUC' => '6',
        ];

        // Obtener fecha en formato ISO con timezone de Lima
        $fechaEmision = $sale->created_at->setTimezone('America/Lima');

        // Construir datos de la empresa
        $companyData = [
            'ruc' => $company->ruc,
            'razonSocial' => $company->razon_social,
            'nombreComercial' => $company->nombre_comercial ?? $company->razon_social,
            'address' => [
                'ubigueo' => $company->ubigueo ?? '',
                'departamento' => $company->departamento ?? '',
                'provincia' => $company->provincia ?? '',
                'distrito' => $company->distrito ?? '',
                'urbanizacion' => $company->urbanizacion ?? '-',
                'direccion' => $company->direccion ?? '',
                'codLocal' => $company->cod_local ?? '0000',
            ]
        ];

        // Construir datos del cliente
        // Si es boleta simple (sin cliente), usar valores especiales de SUNAT
        if ($sale->tipo_comprobante === 'boleta' && !$sale->client) {
            $clientData = [
                'tipoDoc' => '-',
                'numDoc' => '-',
                'rznSocial' => 'CLIENTE VARIOS',
            ];
        } else {
            // Cliente normal con documento
            $clientData = [
                'tipoDoc' => $tipoDocClienteMap[$sale->client->tipo_documento] ?? '1',
                'numDoc' => $sale->client->nro_documento,
                'rznSocial' => $sale->client->nombre_completo,
            ];
        }

        // Construir detalles
        $details = [];
        foreach ($sale->details as $detail) {
            $vendible = $detail->vendible;
            
            if ($vendible instanceof Presentation) {
                // Calcular valores para el detalle
                $cantidad = (float) $detail->cantidad;
                $precioUnitario = (float) $detail->precio_unitario; // Precio CON IGV incluido
                $descuento = (float) ($detail->descuento ?? 0);
                
                // TipAfeIgv de la presentación
                $tipAfeIgv = (int) ($vendible->tipAfeIgv ?? 10);
                
                // Calcular IGV solo si es gravado (tipAfeIgv = 10)
                $mtoBaseIgv = 0;
                $porcentajeIgv = 0;
                $igv = 0;
                $mtoValorVenta = 0;
                $mtoValorUnitario = 0;
                
                if ($tipAfeIgv == 10) {
                    // El precio unitario YA INCLUYE IGV, debemos extraerlo
                    // Precio sin IGV = Precio con IGV / 1.18
                    $mtoValorUnitario = round($precioUnitario / 1.18, 2);
                    
                    // Valor de venta total sin IGV (antes de descuento)
                    $mtoValorVentaSinDescuento = round($mtoValorUnitario * $cantidad, 2);
                    
                    // Descuento sin IGV (si el descuento se aplica sobre el total con IGV)
                    $descuentoSinIgv = round($descuento / 1.18, 2);
                    
                    // Monto valor venta sin IGV (después de descuento)
                    $mtoValorVenta = round($mtoValorVentaSinDescuento - $descuentoSinIgv, 2);
                    
                    // Base IGV (valor de venta sin IGV, después de descuento)
                    $mtoBaseIgv = $mtoValorVenta;
                    
                    // IGV extraído del precio (18% sobre la base)
                    $porcentajeIgv = 18.00;
                    $igv = round($mtoBaseIgv * 0.18, 2);
                } else {
                    // Para exonerado, inafecto, etc. el precio no tiene IGV
                    $mtoValorUnitario = $precioUnitario;
                    $mtoValorVenta = round(($precioUnitario * $cantidad) - $descuento, 2);
                    $mtoBaseIgv = $mtoValorVenta;
                    $porcentajeIgv = 0;
                    $igv = 0;
                }
                
                $totalImpuestos = $igv;
                $mtoPrecioUnitario = $precioUnitario; // Precio con IGV (ya incluye IGV)
                
                // Obtener unidad SUNAT
                $unidad = 'NIU'; // Por defecto
                if ($vendible->unitSunat) {
                    $unidad = $vendible->unitSunat->code;
                }
                
                // Descripción del producto
                $descripcion = $vendible->nombre;
                if ($vendible->product) {
                    $descripcion = $vendible->product->nombre . ' - ' . $vendible->nombre;
                }
                
                // Código de producto (barcode)
                $codProducto = $vendible->barcode ?? '';

                $details[] = [
                    'tipAfeIgv' => $tipAfeIgv,
                    'codProducto' => $codProducto,
                    'unidad' => $unidad,
                    'descripcion' => $descripcion,
                    'cantidad' => $cantidad,
                    'mtoValorUnitario' => round($mtoValorUnitario, 2), // Precio sin IGV
                    'mtoValorVenta' => round($mtoValorVenta, 2), // Valor de venta sin IGV
                    'mtoBaseIgv' => round($mtoBaseIgv, 2), // Base para IGV
                    'porcentajeIgv' => $porcentajeIgv,
                    'igv' => round($igv, 2), // IGV extraído
                    'totalImpuestos' => round($totalImpuestos, 2),
                    'mtoPrecioUnitario' => round($mtoPrecioUnitario, 2), // Precio con IGV
                ];
            }
            // Las promociones se pueden manejar de forma similar si es necesario
        }

        return [
            'ublVersion' => '2.1',
            'tipoOperacion' => '0101',
            'tipoDoc' => $tipoDocMap[$sale->tipo_comprobante] ?? '03',
            'serie' => $sale->serie,
            'correlativo' => (string) $sale->correlativo,
            'fechaEmision' => $fechaEmision->format('c'), // ISO 8601 con timezone
            'tipoMoneda' => 'PEN',
            'company' => $companyData,
            'client' => $clientData,
            'details' => $details,
        ];
    }

    /**
     * Guardar respuesta de SUNAT en la base de datos
     */
    private function guardarRespuestaSunat(Sale $sale, array $response, $see)
    {
        $estado = null;
        $codigo = null;
        $descripcion = null;
        $observaciones = null;
        $errorCode = null;
        $errorMessage = null;
        $xml = null;
        $hash = null;
        $cdrZip = null;

        if (!$response['success']) {
            // Error de conexión
            $estado = 'error_conexion';
            $errorCode = $response['error']['code'] ?? null;
            $errorMessage = $response['error']['message'] ?? null;
        } else {
            // Respuesta exitosa de SUNAT
            $cdrResponse = $response['cdrResponse'] ?? null;
            if ($cdrResponse) {
                $codigo = (int) $cdrResponse['code'];
                $descripcion = $cdrResponse['description'] ?? null;
                $observaciones = $cdrResponse['notes'] ?? null;

                // Determinar estado según código
                if ($codigo === 0) {
                    $estado = 'aceptada';
                } elseif ($codigo >= 2000 && $codigo <= 3999) {
                    $estado = 'rechazada';
                } else {
                    $estado = 'excepcion';
                }
            }

            // Obtener XML y hash si están disponibles
            if (isset($response['xml'])) {
                $xml = $response['xml'];
            } elseif (method_exists($see, 'getFactory')) {
                $xml = $see->getFactory()->getLastXml();
            }

            if (isset($response['hash'])) {
                $hash = $response['hash'];
            } elseif ($xml) {
                $xmlUtils = new XmlUtils();
                $hash = $xmlUtils->getHashSign($xml);
            }

            if (isset($response['cdrZip'])) {
                $cdrZip = $response['cdrZip'];
            }
        }

        // Guardar en la base de datos
        SunatResponse::create([
            'sale_id' => $sale->id,
            'estado' => $estado,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'observaciones' => $observaciones,
            'xml' => $xml,
            'hash' => $hash,
            'cdr_zip' => $cdrZip,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }
}
