<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    /*
     * Public routes
     */
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    /*
     * Protected routes
     */
    Route::middleware('auth:api')->group(function () {
        Route::get('user', function () {
            return response()->json(['data' => auth()->user()]);
        });

        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::prefix('invoices')->group(function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::post('/', [InvoiceController::class, 'store']);
            Route::get('/{salesOrderNumber}', [InvoiceController::class, 'show']);
            Route::put('/{salesOrderNumber}', [InvoiceController::class, 'update']);
            Route::delete('/{salesOrderNumber}', [InvoiceController::class, 'destroy']);
        });

        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/', [CustomerController::class, 'store']);
            Route::get('/{id}', [CustomerController::class, 'show']);
            Route::put('/{id}', [CustomerController::class, 'update']);
            Route::delete('/{id}', [CustomerController::class, 'destroy']);
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });

        Route::prefix('warehouses')->group(function () {
            Route::get('/', [WarehouseController::class, 'index']);
            Route::post('/', [WarehouseController::class, 'store']);
            Route::get('/{name}', [WarehouseController::class, 'show']);
            Route::put('/{id}', [WarehouseController::class, 'update']);
            Route::delete('/{id}', [WarehouseController::class, 'destroy']);
            Route::get('/{name}/product', [WarehouseController::class, 'availableProducts']);
        });

    });

});
