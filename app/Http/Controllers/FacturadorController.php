<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SunatResponse;
use Illuminate\Http\Request;

class FacturadorController extends Controller
{
    /**
     * Listar facturas, boletas y notas de crédito (mismo patrón que otros CRUDs).
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'facturas');
        if (!in_array($tab, ['facturas', 'boletas', 'notas'], true)) {
            $tab = 'facturas';
        }

        $facturas = Sale::with(['client', 'user'])
            ->where('tipo_comprobante', 'factura')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'facturas_page');

        $boletas = Sale::with(['client', 'user'])
            ->where('tipo_comprobante', 'boleta')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'boletas_page');

        // Incluir tipo_documento = 'nota_credito' y también respuestas con descripción de NC (datos legacy)
        $notas = SunatResponse::with(['sale.client', 'sale.user'])
            ->where(function ($q) {
                $q->where('tipo_documento', 'nota_credito')
                    ->orWhere(function ($q2) {
                        $q2->where('tipo_documento', 'comprobante')
                            ->where(function ($q3) {
                                $q3->where('descripcion', 'like', '%Nota de Credito%')
                                    ->orWhere('descripcion', 'like', '%Nota de Crédito%');
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'notas_page');

        return view('facturador.index', compact('facturas', 'boletas', 'notas', 'tab'));
    }
}
