<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::get('health', HealthController::class)->name('health');

        Route::middleware(['auth:sanctum', 'permission:access api'])->group(function (): void {
            // Endpoint API terlindung akan ditambah dalam sprint seterusnya.
        });
    });
