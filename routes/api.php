<?php

use App\Http\Controllers\Api\V1\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Api\V1\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\LookupController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::get('health', HealthController::class)->name('health');

        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->name('login');

        Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        Route::get('areas', [LookupController::class, 'areas'])->name('areas.index');
        Route::get('categories', [LookupController::class, 'categories'])->name('categories.index');
        Route::get('facilities', [LookupController::class, 'facilities'])->name('facilities.index');
        Route::post('reports', [ReportController::class, 'store'])
            ->middleware('throttle:public-reports')
            ->name('reports.store');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('profile', [AuthController::class, 'profile'])->name('profile');

            Route::prefix('admin')
                ->name('admin.')
                ->middleware('permission:access api')
                ->group(function (): void {
                    Route::get('properties', [AdminPropertyController::class, 'index'])
                        ->middleware('permission:view properties')
                        ->name('properties.index');
                    Route::post('properties', [AdminPropertyController::class, 'store'])
                        ->middleware('permission:create properties')
                        ->name('properties.store');
                    Route::put('properties/{property}', [AdminPropertyController::class, 'update'])
                        ->middleware('permission:edit properties')
                        ->name('properties.update');
                    Route::patch('properties/{property}/availability', [AdminPropertyController::class, 'availability'])
                        ->middleware('permission:update property availability')
                        ->name('properties.availability');
                    Route::patch('properties/{property}/verify', [AdminPropertyController::class, 'verify'])
                        ->middleware('permission:verify properties')
                        ->name('properties.verify');

                    Route::get('reports', [AdminReportController::class, 'index'])
                        ->middleware('permission:view reports')
                        ->name('reports.index');
                    Route::patch('reports/{report}/resolve', [AdminReportController::class, 'resolve'])
                        ->middleware('permission:resolve reports')
                        ->name('reports.resolve');
                });
        });
    });
