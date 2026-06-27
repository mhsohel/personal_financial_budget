<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FCM & Push Notifications
    Route::post('/fcm-token', [ProfileController::class, 'updateFcmToken'])->name('profile.fcm-token');
    Route::get('/firebase-config', function () {
        return response()->json([
            'apiKey' => config('services.firebase.api_key'),
            'authDomain' => config('services.firebase.auth_domain'),
            'projectId' => config('services.firebase.project_id'),
            'storageBucket' => config('services.firebase.storage_bucket'),
            'messagingSenderId' => config('services.firebase.messaging_sender_id'),
            'appId' => config('services.firebase.app_id'),
        ]);
    })->name('firebase.config');

    // Categories
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Accounts
    Route::post('/accounts', [App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
    Route::patch('/accounts/{account}', [App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{account}', [App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');

    // Budgets
    Route::post('/budgets', [App\Http\Controllers\BudgetController::class, 'store'])->name('budgets.store');

    // Transactions
    Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
    Route::patch('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Transfers
    Route::post('/transfers', [App\Http\Controllers\TransferController::class, 'store'])->name('transfers.store');
    Route::patch('/transfers/{transaction}', [App\Http\Controllers\TransferController::class, 'update'])->name('transfers.update');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/forecast', [App\Http\Controllers\ReportController::class, 'forecast'])->name('reports.forecast');

    // SaaS Licenses
    Route::get('/licenses', [App\Http\Controllers\LicenseController::class, 'index'])->name('licenses.index');
    Route::post('/clients', [App\Http\Controllers\LicenseController::class, 'storeClient'])->name('clients.store');
    Route::post('/licenses', [App\Http\Controllers\LicenseController::class, 'storeLicense'])->name('licenses.store');
    Route::post('/licenses/{license}/pay', [App\Http\Controllers\LicenseController::class, 'logPayment'])->name('licenses.pay');
    Route::patch('/licenses/{license}', [App\Http\Controllers\LicenseController::class, 'updateLicense'])->name('licenses.update');
    Route::delete('/clients/{client}', [App\Http\Controllers\LicenseController::class, 'destroyClient'])->name('clients.destroy');
    Route::delete('/licenses/{license}', [App\Http\Controllers\LicenseController::class, 'destroyLicense'])->name('licenses.destroy');

    // Loans & Debts
    Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{loan}/repayment', [App\Http\Controllers\LoanController::class, 'logRepayment'])->name('loans.repayment');
    Route::delete('/loans/{loan}', [App\Http\Controllers\LoanController::class, 'destroy'])->name('loans.destroy');

    // Recurring Schedules
    Route::get('/recurring', [App\Http\Controllers\RecurringScheduleController::class, 'index'])->name('recurring.index');
    Route::post('/recurring', [App\Http\Controllers\RecurringScheduleController::class, 'store'])->name('recurring.store');
    Route::patch('/recurring/{recurringSchedule}', [App\Http\Controllers\RecurringScheduleController::class, 'update'])->name('recurring.update');
    Route::delete('/recurring/{recurringSchedule}', [App\Http\Controllers\RecurringScheduleController::class, 'destroy'])->name('recurring.destroy');
    Route::post('/recurring/{recurringSchedule}/process', [App\Http\Controllers\RecurringScheduleController::class, 'process'])->name('recurring.process');
    Route::post('/recurring/{recurringSchedule}/skip', [App\Http\Controllers\RecurringScheduleController::class, 'skip'])->name('recurring.skip');
    Route::post('/recurring/{recurringSchedule}/toggle', [App\Http\Controllers\RecurringScheduleController::class, 'toggle'])->name('recurring.toggle');
});

require __DIR__.'/auth.php';
