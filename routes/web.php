<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MigrationJobController;

use App\Http\Controllers\WebMigrationController;

Route::get('/', [WebMigrationController::class, 'index'])->name('dashboard');
Route::get('/migrations/create', [WebMigrationController::class, 'create'])->name('migrations.create');
Route::post('/migrations', [WebMigrationController::class, 'store'])->name('migrations.store');
Route::get('/migrations/{id}/map', [WebMigrationController::class, 'showMapping'])->name('migrations.map');
Route::post('/migrations/{id}/map', [WebMigrationController::class, 'submitMapping'])->name('migrations.submit_mapping');
Route::get('/migrations/{id}/logs', [WebMigrationController::class, 'showLogs'])->name('migrations.logs');
Route::delete('/migrations/{id}', [WebMigrationController::class, 'destroy'])->name('migrations.destroy');

// Since api.php is not installed by default in Laravel 11, we place them here 
// for development under an /api prefix.
Route::prefix('api')->group(function () {
    Route::get('/migration-jobs', [MigrationJobController::class, 'index']);
    Route::post('/migration-jobs', [MigrationJobController::class, 'store']);
    Route::get('/migration-jobs/{id}', [MigrationJobController::class, 'show']);
    Route::post('/migration-jobs/{id}/process', [MigrationJobController::class, 'process']);
});
