<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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
    Route::resource('tandons', TandonController::class);
    Route::get('/tandons/{tandon}/readings', [TandonReadingController::class, 'index'])->name('tandon-readings.index');
    Route::get('/tandons/{tandon}/readings/create', [TandonReadingController::class, 'create'])->name('tandon-readings.create');
    Route::post('/tandons/{tandon}/readings', [TandonReadingController::class, 'store'])->name('tandon-readings.store');
    Route::get('/tandons/{tandon}/readings/{reading}', [TandonReadingController::class, 'show'])->name('tandon-readings.show');
    Route::delete('/tandons/{tandon}/readings/{reading}', [TandonReadingController::class, 'destroy'])->name('tandon-readings.destroy');
});
