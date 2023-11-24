<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
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

Route::get('/', [ArticleController::class, 'index']);
Route::get('/{article}', [ArticleController::class, 'show']);

Route::group([
    'middleware' => ['guest:sanctum']
], function () {
    Route::post('/sign', [AuthController::class, 'newStore']);

    Route::post('/login/access-token', [AuthController::class, 'store']);
});

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    Route::post('/create-article', [ArticleController::class, 'store']);

    Route::put('/{article}/edit', [ArticleController::class, 'update']);

    Route::delete('/{article}', [ArticleController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

