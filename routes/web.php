<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyReportController;
use App\Http\Controllers\PublicPortalController;
use Illuminate\Support\Facades\Route;

Route::middleware('track.public.visitor')->group(function (): void {
    Route::get('/', [PublicPortalController::class, 'home'])->name('home');
    Route::view('/rumah-sewa', 'public.properties.index')->name('properties.index');
    Route::get('/rumah-sewa/{property}', [PublicPortalController::class, 'show'])->name('properties.show');
    Route::get('/aduan', [PropertyReportController::class, 'create'])->name('reports.create');
    Route::get('/rumah-sewa/{property}/aduan', [PropertyReportController::class, 'createForProperty'])->name('reports.create.property');
});
Route::post('/aduan', [PropertyReportController::class, 'store'])
    ->middleware('throttle:public-reports')
    ->name('reports.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
