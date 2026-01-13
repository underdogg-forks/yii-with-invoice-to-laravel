<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientPeppolController;
use App\Http\Controllers\PaymentPeppolController;
use App\Http\Controllers\UnitPeppolController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TaxRateController;

Route::get('/', function () {
    return view('welcome');
});

// Peppol Client Routes - Protected with auth middleware
Route::middleware('auth')->prefix('clientpeppol')->name('clientpeppol.')->group(function () {
    Route::get('/', [ClientPeppolController::class, 'index'])->name('index');
    Route::get('/add/{client_id}', [ClientPeppolController::class, 'add'])->name('add');
    Route::post('/add/{client_id}', [ClientPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ClientPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [ClientPeppolController::class, 'update'])->name('update');
    Route::get('/view/{id}', [ClientPeppolController::class, 'view'])->name('view');
    Route::delete('/delete/{id}', [ClientPeppolController::class, 'delete'])->name('delete');
});

// Peppol Payment Routes - Protected with auth middleware
Route::middleware('auth')->prefix('paymentpeppol')->name('paymentpeppol.')->group(function () {
    Route::get('/', [PaymentPeppolController::class, 'index'])->name('index');
    Route::get('/add/{inv_id}', [PaymentPeppolController::class, 'add'])->name('add');
    Route::post('/add/{inv_id}', [PaymentPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [PaymentPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [PaymentPeppolController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [PaymentPeppolController::class, 'delete'])->name('delete');
});

// Peppol Unit Routes - Protected with auth middleware
Route::middleware('auth')->prefix('unitpeppol')->name('unitpeppol.')->group(function () {
    Route::get('/', [UnitPeppolController::class, 'index'])->name('index');
    Route::get('/add/{unit_id}', [UnitPeppolController::class, 'add'])->name('add');
    Route::post('/add/{unit_id}', [UnitPeppolController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [UnitPeppolController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [UnitPeppolController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [UnitPeppolController::class, 'delete'])->name('delete');
});

// Invoice Routes - Protected with auth and permission middleware
Route::middleware(['auth', 'permission:manage-invoices'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{id}/status', [InvoiceController::class, 'changeStatus'])->name('invoices.change-status');
    Route::post('/invoices/{id}/credit', [InvoiceController::class, 'createCredit'])->name('invoices.create-credit');
});

// Product Routes - Protected with auth and permission middleware
Route::middleware(['auth', 'permission:manage-products'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
});

// Tax Rate Routes - Protected with auth and permission middleware
Route::middleware(['auth', 'permission:manage-invoices'])->group(function () {
    Route::resource('tax-rates', TaxRateController::class)->except(['show']);
});
