<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientPeppolController;
use App\Http\Controllers\PaymentPeppolController;
use App\Http\Controllers\UnitPeppolController;

Route::get('/', function () {
    return view('welcome');
});

// Peppol Client Routes
Route::prefix('clientpeppol')->name('clientpeppol.')->group(function () {
    Route::get('/', [ClientPeppolController::class, 'index'])->name('index');
    Route::get('/add/{client_id}', [ClientPeppolController::class, 'add'])->name('add');
    Route::post('/add/{client_id}', [ClientPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ClientPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [ClientPeppolController::class, 'update'])->name('update');
    Route::get('/view/{id}', [ClientPeppolController::class, 'view'])->name('view');
    Route::delete('/delete/{id}', [ClientPeppolController::class, 'delete'])->name('delete');
});

// Peppol Payment Routes
Route::prefix('paymentpeppol')->name('paymentpeppol.')->group(function () {
    Route::get('/', [PaymentPeppolController::class, 'index'])->name('index');
    Route::get('/add/{inv_id}', [PaymentPeppolController::class, 'add'])->name('add');
    Route::post('/add/{inv_id}', [PaymentPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [PaymentPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [PaymentPeppolController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [PaymentPeppolController::class, 'delete'])->name('delete');
});

// Peppol Unit Routes
Route::prefix('unitpeppol')->name('unitpeppol.')->group(function () {
    Route::get('/', [UnitPeppolController::class, 'index'])->name('index');
    Route::get('/add/{unit_id}', [UnitPeppolController::class, 'add'])->name('add');
    Route::post('/add/{unit_id}', [UnitPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [UnitPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [UnitPeppolController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [UnitPeppolController::class, 'delete'])->name('delete');
});
