<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('assets', AssetController::class);

Route::get('maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
Route::post('assets/{asset}/maintenances', [MaintenanceController::class, 'store'])->name('assets.maintenances.store');
Route::delete('maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
