<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SalesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//ITEMS
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/q/{query}', [ItemController::class, 'index']);
Route::get('/items/{id}', [ItemController::class, 'show']);
Route::delete('/items/{id}', [ItemController::class, 'destroy']);
Route::post('/items', [ItemController::class, 'store']);
Route::put('/items/{id}', [ItemController::class, 'update']);

//CUSTOMERS
Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customers/{id}', [CustomerController::class, 'show']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);

//SALES
Route::get('/sales', [SalesController::class, 'index']);
Route::get('/sales/{id}', [SalesController::class, 'show']);
Route::delete('/sales/{id}', [SalesController::class, 'destroy']);
Route::post('/sales', [SalesController::class, 'store']);
Route::put('/sales/{id}', [SalesController::class, 'update']);