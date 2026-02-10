<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TandonController;
use App\Http\Controllers\TandonReadingController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Alerts routes
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{alert}', [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/alerts/{alert}/unresolve', [AlertController::class, 'unresolve'])->name('alerts.unresolve');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
    Route::post('/alerts/resolve-all', [AlertController::class, 'resolveAll'])->name('alerts.resolveAll');
    
    // Tandons routes
    Route::resource('tandons', TandonController::class);
    // Route to truncate readings and usages for a tank
    Route::post('/tandons/{tandon}/truncate-data', [TandonController::class, 'truncateData'])->name('tandons.truncate-data');
    
    // Tandon readings routes
    Route::get('/tandons/{tandon}/readings', [TandonReadingController::class, 'index'])->name('tandon-readings.index');
    Route::get('/tandons/{tandon}/readings/create', [TandonReadingController::class, 'create'])->name('tandon-readings.create');
    Route::post('/tandons/{tandon}/readings', [TandonReadingController::class, 'store'])->name('tandon-readings.store');
    Route::get('/tandons/{tandon}/readings/{reading}', [TandonReadingController::class, 'show'])->name('tandon-readings.show');
    Route::delete('/tandons/{tandon}/readings/{reading}', [TandonReadingController::class, 'destroy'])->name('tandon-readings.destroy');
    Route::post('/tandons/{tandon}/readings/bulk-delete', [TandonReadingController::class, 'bulkDestroy'])->name('tandon-readings.bulk-destroy');
});
