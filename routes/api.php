<?php

use App\Http\Controllers\HashHandlerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::middleware(['throttle:hash-generate'])->group(function(){
    Route::get('/hash/generate/{str}', [HashHandlerController::class, 'generate']);
});

Route::get('/hash/results/{page}', [HashHandlerController::class, 'results']);
