<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\StatusSummaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\InsurancePolicyController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// Per-project dashboard view
Route::get('/dashboard/project/{id}', [ProjectController::class, 'dashboard'])->name('projects.dashboard');

Route::get('/overview', function () {
    return view('overview');
});

// Insurance Tracker routes will be added in protected routes section

// Additional route for project detail (if needed as a separate route)
Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');

// Data Import
Route::get('/data-import', [DataImportController::class, 'index'])->name('data-import.index');
Route::post('/data-import/analyze', [DataImportController::class, 'analyze'])->name('data-import.analyze');
Route::post('/data-import/commit', [DataImportController::class, 'commit'])->name('data-import.commit');


use App\Http\Controllers\AuthController;


// Public Routes
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Require Login)
Route::middleware(['auth'])->group(function () {
    
    // PM & Tech & General Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Finance Routes
    Route::get('/finance-overview', [FinanceController::class, 'overview'])->name('finance.overview');
    Route::get('/finance-tracker', [FinanceController::class, 'tracker'])->name('finance.tracker');
    Route::get('/finance-tracker/{projectId}', [FinanceController::class, 'tracker'])->name('finance.tracker.project');
    Route::post('/finance-tracker', [FinanceController::class, 'store'])->name('finance.tracker.store');
    Route::delete('/finance-tracker/payment/{paymentId}', [FinanceController::class, 'destroyPayment'])->name('finance.tracker.payment.destroy');

    // Supply Chain Access - Materials/Inventory
    Route::get('/inventory', [ItemController::class, 'index'])->name('inventory');

    // Client Management
    Route::resource('clients', ClientController::class);
    
    // Project Management
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/materials/edit', [ProjectController::class, 'editMaterials'])->name('projects.materials.edit');
    Route::put('/projects/{project}/materials', [ProjectController::class, 'updateMaterials'])->name('projects.materials.update');
    Route::post('/projects/{project}/files', [ProjectFileController::class, 'store'])->name('projects.files.store');
    Route::get('/projects/{project}/files/{file}', [ProjectFileController::class, 'download'])->name('projects.files.download');
    Route::delete('/projects/{project}/files/{file}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');
    
    // Status Summary
    Route::get('/status-summary', [StatusSummaryController::class, 'index'])->name('status-summary.index');

    // Insurance Tracker
    Route::get('/insurance-tracker', [InsurancePolicyController::class, 'index'])->name('insurance-tracker.index');
    Route::post('/insurance-tracker', [InsurancePolicyController::class, 'store'])->name('insurance-tracker.store');
    Route::delete('/insurance-tracker/{insurancePolicy}', [InsurancePolicyController::class, 'destroy'])->name('insurance-tracker.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User management (Project Manager only)
    Route::post('/users', [ProfileController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [ProfileController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [ProfileController::class, 'destroyUser'])->name('users.destroy');
});