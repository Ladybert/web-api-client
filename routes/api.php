<?php

use App\Http\Controllers\Api\UnitTypeController;
use App\Http\Controllers\Api\ResidentialEstateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/unit-type', UnitTypeController::class);
Route::apiResource('/residential-estate', ResidentialEstateController::class);
