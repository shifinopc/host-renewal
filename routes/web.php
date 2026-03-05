<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.attempt')
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('servers', ServerController::class)->except('show')->middleware('admin');
    Route::resource('customers', CustomerController::class);
    Route::get('domains/{domain}/details', [DomainController::class, 'details'])->name('domains.details');
    Route::prefix('domains/{domain}')->group(function () {
        Route::get('renew/modal', [DomainController::class, 'renewModal'])->name('domains.renew.modal');
        Route::post('renew', [DomainController::class, 'renew'])->name('domains.renew');
        Route::get('payments/modal', [PaymentController::class, 'indexModal'])->name('payments.index.modal');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/create/modal', [PaymentController::class, 'createModal'])->name('payments.create.modal');
        Route::get('proforma/create/modal', [PaymentController::class, 'createProformaModal'])->name('proforma.create.modal');
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });
    Route::resource('domains', DomainController::class);

    Route::get('payments', [PaymentController::class, 'all'])->name('payments.all');
    Route::get('payments/export', [PaymentController::class, 'export'])->name('payments.export');
    Route::post('payments/add', [PaymentController::class, 'quickStore'])->name('payments.quickStore');
    Route::get('payments/reports', [PaymentController::class, 'reports'])->name('payments.reports');
    Route::get('proforma-invoices', [PaymentController::class, 'proformaIndex'])->name('proforma-invoices.index');
    Route::get('proforma-invoices/create', [PaymentController::class, 'createProforma'])->name('proforma-invoices.create');
    Route::post('proforma-invoices', [PaymentController::class, 'storeProforma'])->name('proforma-invoices.store');
    Route::get('proforma-invoices/{payment}/edit', [PaymentController::class, 'editProformaModal'])->name('proforma-invoices.edit');
    Route::put('proforma-invoices/{payment}', [PaymentController::class, 'updateProforma'])->name('proforma-invoices.update');
    Route::get('proforma-invoices/{payment}/view', [PaymentController::class, 'proformaPanel'])->name('proforma-invoices.panel');
    Route::get('payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::middleware('admin')->group(function () {
        Route::get('reports/expiring', [ReportController::class, 'expiring'])->name('reports.expiring');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/server-revenue', [ReportController::class, 'serverRevenue'])->name('reports.serverRevenue');

        Route::get('reports/expiring.csv', [ReportController::class, 'expiringCsv'])->name('reports.expiring.csv');
        Route::get('reports/revenue.csv', [ReportController::class, 'revenueCsv'])->name('reports.revenue.csv');
        Route::get('reports/server-revenue.csv', [ReportController::class, 'serverRevenueCsv'])->name('reports.serverRevenue.csv');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
