<?php

use App\Http\Controllers\Crm\CrmController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/crm')
    ->middleware(['auth:sanctum', 'ability:crm:read', 'throttle:crm'])
    ->group(function () {
        Route::get('guides',                  [CrmController::class, 'guides']);
        Route::get('voyageurs',               [CrmController::class, 'voyageurs']);
        Route::get('experiences',             [CrmController::class, 'experiences']);
        Route::get('reservations',            [CrmController::class, 'reservations']);
        Route::get('reservations/incomplete', [CrmController::class, 'reservationsIncomplete']);
        Route::get('trackings',               [CrmController::class, 'trackings']);
    });
