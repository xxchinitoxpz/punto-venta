<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Presentation;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'brand', 'presentations'])
            ->withCount('presentations')
            ->paginate(10);
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('nombre')->get();
        $brands = Brand::orderBy('nombre')->get();
        
        return view('products.form', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categories,id',
            'marca_id' => 'required|exists:brands,id',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.nombre' => 'required|string|max:255',
            'presentaciones.*.barcode' => 'required|string|max:255|unique:presentations,barcode',
            'presentaciones.*.precio_venta' => 'required|numeric|min:0',
            'presentaciones.*.unidades' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'categoria_id' => $validated['categoria_id'],
                'marca_id' => $validated['marca_id'],
            ]);

            foreach ($validated['presentaciones'] as $presentacionData) {
                Presentation::create([
                    'product_id' => $product->id,
                    'nombre' => $presentacionData['nombre'],
                    'barcode' => $presentacionData['barcode'],
                    'precio_venta' => $presentacionData['precio_venta'],
                    'unidades' => $presentacionData['unidades'],
                ]);
            }

            DB::commit();
            session()->flash('success', 'Producto creado exitosamente con sus presentaciones.');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear el producto: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'presentations']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('presentations');
        $categories = Category::orderBy('nombre')->get();
        $brands = Brand::orderBy('nombre')->get();
        
        return view('products.form', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categories,id',
            'marca_id' => 'required|exists:brands,id',
            'presentaciones' => 'nullable|array',
            'presentaciones.*.id' => 'nullable|exists:presentations,id',
            'presentaciones.*.nombre' => 'required_with:presentaciones|string|max:255',
            'presentaciones.*.barcode' => 'required_with:presentaciones|string|max:255',
            'presentaciones.*.precio_venta' => 'required_with:presentaciones|numeric|min:0',
            'presentaciones.*.unidades' => 'required_with:presentaciones|numeric|min:0.01',
            'presentaciones_eliminar' => 'nullable|array',
            'presentaciones_eliminar.*' => 'exists:presentations,id',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar producto
            $product->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'categoria_id' => $validated['categoria_id'],
                'marca_id' => $validated['marca_id'],
            ]);

            // Eliminar presentaciones marcadas
            if (isset($validated['presentaciones_eliminar']) && !empty($validated['presentaciones_eliminar'])) {
                Presentation::whereIn('id', $validated['presentaciones_eliminar'])
                    ->where('product_id', $product->id)
                    ->delete();
            }

            // Procesar presentaciones
            if (isset($validated['presentaciones']) && !empty($validated['presentaciones'])) {
                foreach ($validated['presentaciones'] as $presentacionData) {
                    if (isset($presentacionData['id']) && $presentacionData['id']) {
                        // Actualizar presentación existente
                        $presentation = Presentation::find($presentacionData['id']);
                        if ($presentation && $presentation->product_id == $product->id) {
                            // Validar barcode único (excepto para esta presentación)
                            $barcodeExists = Presentation::where('barcode', $presentacionData['barcode'])
                                ->where('id', '!=', $presentation->id)
                                ->exists();
                            
                            if ($barcodeExists) {
                                throw new \Exception('El código de barras ya está en uso.');
                            }

                            $presentation->update([
                                'nombre' => $presentacionData['nombre'],
                                'barcode' => $presentacionData['barcode'],
                                'precio_venta' => $presentacionData['precio_venta'],
                                'unidades' => $presentacionData['unidades'],
                            ]);
                        }
                    } else {
                        // Crear nueva presentación
                        $barcodeExists = Presentation::where('barcode', $presentacionData['barcode'])->exists();
                        if ($barcodeExists) {
                            throw new \Exception('El código de barras ya está en uso.');
                        }

                        Presentation::create([
                            'product_id' => $product->id,
                            'nombre' => $presentacionData['nombre'],
                            'barcode' => $presentacionData['barcode'],
                            'precio_venta' => $presentacionData['precio_venta'],
                            'unidades' => $presentacionData['unidades'],
                        ]);
                    }
                }
            }

            DB::commit();
            session()->flash('success', 'Producto actualizado exitosamente.');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el producto: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Las presentaciones se eliminarán en cascada
            $product->delete();
            
            DB::commit();
            session()->flash('success', 'Producto eliminado exitosamente.');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al eliminar el producto: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
