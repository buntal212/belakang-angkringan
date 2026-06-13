<?php

use App\Http\Controllers\Transaksi\Penjualan\PenjualanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Master\MasterAngkringanController;
use App\Http\Controllers\Master\MasterMenuController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (butuh token Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/master-angkringan', [MasterAngkringanController::class, 'index']);

    // Master Menu CRUD
    Route::get('/master-menu', [MasterMenuController::class, 'index']);
    Route::post('/master-menu', [MasterMenuController::class, 'store']);
    Route::put('/master-menu/{kodemenu}', [MasterMenuController::class, 'update']);
    Route::delete('/master-menu/{kodemenu}', [MasterMenuController::class, 'destroy']);

    Route::get('/penjualan-getlist', [PenjualanController::class, 'index']);
    Route::post('/penjualan-simpan', [PenjualanController::class, 'store']);
});
