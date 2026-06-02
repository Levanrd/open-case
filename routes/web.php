<?php

use App\Http\Controllers\Accountant\CaseController as AccountantCaseController;
use App\Http\Controllers\Admin\AccountantController;
use App\Http\Controllers\Admin\CaseController as AdminCaseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Owner\CaseController as OwnerCaseController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Landing'))->name('home');
Route::get('/privacy', fn () => Inertia::render('Legal/Privacy'))->name('privacy');
Route::get('/terms', fn () => Inertia::render('Legal/Terms'))->name('terms');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'update'])->name('password.update');
});

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');
    Route::get('/documents/{document}/download', DocumentController::class)
        ->name('documents.download');

    Route::middleware('role:business_owner')->group(function (): void {
        Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
        Route::get('/cases/new', [OwnerCaseController::class, 'create'])->name('owner.cases.create');
        Route::post('/cases', [OwnerCaseController::class, 'store'])->name('owner.cases.store');
        Route::get('/cases/{taxCase}', [OwnerCaseController::class, 'show'])->name('owner.cases.show');
        Route::post('/cases/{taxCase}/documents', [OwnerCaseController::class, 'uploadDocument'])->name('owner.cases.documents.store');
        Route::post('/cases/{taxCase}/package', [OwnerCaseController::class, 'selectPackage'])->name('owner.cases.package');
        Route::post('/cases/{taxCase}/payment-reference', [OwnerCaseController::class, 'submitPaymentReference'])->name('owner.cases.payment-reference');
        Route::post('/documents/{document}/fulfill', [OwnerCaseController::class, 'fulfillDocumentRequest'])->name('owner.documents.fulfill');
    });

    Route::middleware('role:accountant')->prefix('accountant')->name('accountant.')->group(function (): void {
        Route::get('/cases', [AccountantCaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/{taxCase}', [AccountantCaseController::class, 'show'])->name('cases.show');
        Route::patch('/cases/{taxCase}/status', [AccountantCaseController::class, 'updateStatus'])->name('cases.status');
        Route::post('/cases/{taxCase}/notes', [AccountantCaseController::class, 'storeNote'])->name('cases.notes');
        Route::patch('/checklist/{checklistItem}', [AccountantCaseController::class, 'updateChecklist'])->name('checklist.update');
        Route::post('/cases/{taxCase}/document-requests', [AccountantCaseController::class, 'requestDocument'])->name('documents.request');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/cases', [AdminCaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/{taxCase}', [AdminCaseController::class, 'show'])->name('cases.show');
        Route::patch('/cases/{taxCase}/assign', [AdminCaseController::class, 'assign'])->name('cases.assign');
        Route::patch('/payments/{payment}/verify', [AdminCaseController::class, 'verifyPayment'])->name('payments.verify');
        Route::get('/accountants', [AccountantController::class, 'index'])->name('accountants.index');
        Route::get('/accountants/create', [AccountantController::class, 'create'])->name('accountants.create');
        Route::post('/accountants', [AccountantController::class, 'store'])->name('accountants.store');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::patch('/packages/{servicePackage}', [PackageController::class, 'update'])->name('packages.update');
    });
});
