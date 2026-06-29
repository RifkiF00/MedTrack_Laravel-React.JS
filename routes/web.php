<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AsetController;
use App\Http\Controllers\Web\TrackingController;
use App\Http\Controllers\Web\MutasiController;
use App\Http\Controllers\Web\MaintenanceController;
use App\Http\Controllers\Web\WorkOrderController;
use App\Http\Controllers\Web\DokumenController;
use App\Http\Controllers\Web\DirektoriController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Rute Terproteksi Auth (Dashboard, Aset, Tracking, Profile, Mutasi, PM, WO)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard (Role-based data loading)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/calendar/reschedule', [DashboardController::class, 'rescheduleCalendar'])->name('calendar.reschedule');
    
    // Aset Medis (CRUD)
    Route::get('/aset', [AsetController::class, 'index'])->name('aset.index');
    Route::get('/aset/create', [AsetController::class, 'create'])->name('aset.create');
    Route::post('/aset', [AsetController::class, 'store'])->name('aset.store');
    Route::get('/aset/{id}', [AsetController::class, 'show'])->name('aset.show');
    Route::get('/aset/{id}/edit', [AsetController::class, 'edit'])->name('aset.edit');
    Route::post('/aset/{id}', [AsetController::class, 'update'])->name('aset.update');
    Route::delete('/aset/{id}', [AsetController::class, 'destroy'])->name('aset.destroy');
    
    // GPS Geolocation Tracking Map
    Route::get('/tracking/map', [TrackingController::class, 'map'])->name('tracking.map');
    
    // Mutasi Ruangan
    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
    Route::post('/mutasi/{id}/approve', [MutasiController::class, 'approve'])->name('mutasi.approve');
    Route::post('/mutasi/{id}/reject', [MutasiController::class, 'reject'])->name('mutasi.reject');
    Route::post('/mutasi/{id}/complete', [MutasiController::class, 'complete'])->name('mutasi.complete');

    // Preventive Maintenance
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::post('/maintenance/log', [MaintenanceController::class, 'log'])->name('maintenance.log');
    Route::post('/maintenance/log/{id}', [MaintenanceController::class, 'updateLog'])->name('maintenance.log.update');
    Route::post('/maintenance/log/{id}/delete', [MaintenanceController::class, 'deleteLog'])->name('maintenance.log.delete');
    Route::post('/maintenance/master/{id}', [MaintenanceController::class, 'updateMaster'])->name('maintenance.master.update');
    Route::post('/maintenance/master/{id}/delete', [MaintenanceController::class, 'deleteMaster'])->name('maintenance.master.delete');

    // Work Orders / Troubleshoot Request
    Route::get('/workorder', [WorkOrderController::class, 'index'])->name('workorder.index');
    Route::get('/workorder/create', [WorkOrderController::class, 'create'])->name('workorder.create');
    Route::post('/workorder', [WorkOrderController::class, 'store'])->name('workorder.store');
    Route::post('/workorder/{id}/status', [WorkOrderController::class, 'updateStatus'])->name('workorder.status');
    Route::post('/workorder/{id}/assign', [WorkOrderController::class, 'assignTeknisi'])->name('workorder.assign');
    Route::post('/workorder/{id}/signoff', [WorkOrderController::class, 'signOff'])->name('workorder.signoff');

    // Dokumen Mutu
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/export/{type}', [DokumenController::class, 'exportPdf'])->name('dokumen.export');

    // Direktori Unit & SDM
    Route::get('/direktori', [DirektoriController::class, 'index'])->name('direktori.index');
    Route::post('/direktori/ruangan', [DirektoriController::class, 'storeRuangan'])->name('direktori.ruangan.store');
    Route::post('/direktori/ruangan/{id}', [DirektoriController::class, 'updateRuangan'])->name('direktori.ruangan.update');
    Route::post('/direktori/ruangan/{id}/delete', [DirektoriController::class, 'deleteRuangan'])->name('direktori.ruangan.delete');
    Route::post('/direktori/sdm', [DirektoriController::class, 'storeSDM'])->name('direktori.sdm.store');
    Route::post('/direktori/sdm/{id}', [DirektoriController::class, 'updateSDM'])->name('direktori.sdm.update');
    Route::post('/direktori/sdm/{id}/delete', [DirektoriController::class, 'deleteSDM'])->name('direktori.sdm.delete');
    Route::post('/direktori/kontak/{id}', [DirektoriController::class, 'updateKontak'])->name('direktori.kontak.update');

    // Profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
