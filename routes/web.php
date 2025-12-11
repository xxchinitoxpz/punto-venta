<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DocumentSeriesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CashBoxController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    // Rutas para Roles
    Route::resource('roles', RoleController::class);

    // Rutas para Permisos
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');

    // Rutas para Usuarios
    Route::resource('users', UserController::class);

    // Rutas para Empresas
    Route::resource('companies', CompanyController::class);

    // Rutas para Sucursales
    Route::resource('branches', BranchController::class);

    // Rutas para Series de Documentos
    Route::resource('document-series', DocumentSeriesController::class);

    // Rutas para Categorías
    Route::resource('categories', CategoryController::class);

    // Rutas para Marcas
    Route::resource('brands', BrandController::class);

    // Rutas para Productos
    Route::resource('products', ProductController::class);

    // Rutas para Inventario
    Route::resource('inventories', InventoryController::class);

    // Rutas para Clientes
    Route::resource('clients', ClientController::class);

    // Rutas para Proveedores
    Route::resource('suppliers', SupplierController::class);

    // Rutas para Cajas
    Route::resource('cashboxes', CashBoxController::class);
    Route::get('cashboxes/{cashbox}/open-session', [CashBoxController::class, 'openSession'])->name('cashboxes.openSession');
    Route::post('cashboxes/{cashbox}/open-session', [CashBoxController::class, 'storeOpenSession'])->name('cashboxes.storeOpenSession');
    Route::get('sessions/{session}', [CashBoxController::class, 'showSession'])->name('cashboxes.showSession');
    Route::get('sessions/{session}/close', [CashBoxController::class, 'closeSession'])->name('cashboxes.closeSession');
    Route::post('sessions/{session}/close', [CashBoxController::class, 'storeCloseSession'])->name('cashboxes.storeCloseSession');
    Route::post('sessions/{session}/movements', [CashBoxController::class, 'storeManualMovement'])->name('cashboxes.storeManualMovement');

    // Rutas para Promociones
    Route::resource('promotions', PromotionController::class);

    // Rutas para Ventas
    Route::resource('sales', SaleController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
