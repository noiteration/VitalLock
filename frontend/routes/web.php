<?php

use App\Http\Controllers\HealthDataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VLKeyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/generate-keys', [VLKeyController::class, 'generateKeys'])->name('vlkey.generateKeys');

    Route::get('/healthdata/create', [HealthDataController::class, 'create'])->name('healthdata.create');
    Route::post('/healthdata/store', [HealthDataController::class, 'store'])->name('healthdata.store');
    Route::get('/healthdata/show', [HealthDataController::class, 'show'])->name('healthdata.show');
});

require __DIR__.'/auth.php';
