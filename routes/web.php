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
    
    // ─── View Routes (All Authenticated Users) ───
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/finance-overview', [FinanceController::class, 'overview'])->name('finance.overview');
    Route::get('/finance-tracker', [FinanceController::class, 'tracker'])->name('finance.tracker');
    Route::get('/finance-tracker/{projectId}', [FinanceController::class, 'tracker'])->name('finance.tracker.project');
    Route::get('/inventory', [ItemController::class, 'index'])->name('inventory');
    Route::get('/insurance-tracker', [InsurancePolicyController::class, 'index'])->name('insurance-tracker.index');
    Route::get('/status-summary', [StatusSummaryController::class, 'index'])->name('status-summary.index');
    Route::get('/data-import', [DataImportController::class, 'index'])->name('data-import.index');

    // Profile (all authenticated users can edit own profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ─── Project CRUD (PM + Sales can create; PM only for edit/delete) ───
    // IMPORTANT: /create routes MUST come before /{project} wildcard routes
    Route::middleware('role:Project Manager,Sales')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    });

    Route::middleware('role:Project Manager')->group(function () {
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/projects/{project}/files', [ProjectFileController::class, 'store'])->name('projects.files.store');
        Route::delete('/projects/{project}/files/{file}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');
    });

    // ─── Materials Edit (PM + Supply Chain) ───
    Route::middleware('role:Project Manager,Supply Chain')->group(function () {
        Route::get('/projects/{project}/materials/edit', [ProjectController::class, 'editMaterials'])->name('projects.materials.edit');
        Route::put('/projects/{project}/materials', [ProjectController::class, 'updateMaterials'])->name('projects.materials.update');
    });

    // Project view routes (all authenticated) — wildcards AFTER literal routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/dashboard/project/{id}', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/projects/{project}/files/{file}', [ProjectFileController::class, 'download'])->name('projects.files.download');

    // ─── Client CRUD (PM only) ───
    // IMPORTANT: /create route MUST come before /{client} wildcard route
    Route::middleware('role:Project Manager')->group(function () {
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Client view routes (all authenticated) — wildcards AFTER literal routes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    // ─── Finance CRUD (PM + Finance) ───
    Route::middleware('role:Project Manager,Finance')->group(function () {
        Route::post('/finance-tracker', [FinanceController::class, 'store'])->name('finance.tracker.store');
        Route::delete('/finance-tracker/payment/{paymentId}', [FinanceController::class, 'destroyPayment'])->name('finance.tracker.payment.destroy');
    });

    // ─── Insurance CRUD (PM only) ───
    Route::middleware('role:Project Manager')->group(function () {
        Route::post('/insurance-tracker', [InsurancePolicyController::class, 'store'])->name('insurance-tracker.store');
        Route::delete('/insurance-tracker/{insurancePolicy}', [InsurancePolicyController::class, 'destroy'])->name('insurance-tracker.destroy');
    });

    // ─── Data Import (PM + Supply Chain + Finance) ───
    Route::middleware('role:Project Manager,Supply Chain,Finance')->group(function () {
        Route::post('/data-import/analyze', [DataImportController::class, 'analyze'])->name('data-import.analyze');
        Route::post('/data-import/commit', [DataImportController::class, 'commit'])->name('data-import.commit');
    });

    // ─── User Management (PM + Admin) ───
    Route::middleware('role:Project Manager,Admin')->group(function () {
        Route::post('/users', [ProfileController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [ProfileController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [ProfileController::class, 'destroyUser'])->name('users.destroy');
    });
});