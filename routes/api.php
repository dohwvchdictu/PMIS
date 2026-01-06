<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProcurementController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('procurements', ProcurementController::class)->names([
    'index' => 'api.procurements.index',
    'store' => 'api.procurements.store',
    'show' => 'api.procurements.show',
    'update' => 'api.procurements.update',
    'destroy' => 'api.procurements.destroy',
]);
