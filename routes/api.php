<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * Grupo de rotas de validação  e manipulação de credenciais
 */
Route::group([
    'middleware'=>'api',
    'prefix'=>'auth'
],function ($router) {
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
    Route::post('me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
});

/**
 * Grupo para buscar dados cotação
 */
Route::group([
    'middleware'=>['api', 'jwt.verify'],
    'prefix'=>'moedas'
], function ($router){
    Route::get('moeda', [App\Http\Controllers\CotacaoController::class, 'moedas']);
    Route::get('cotacao/{coin}', [App\Http\Controllers\CotacaoController::class, 'cotacao']);
});

/**
 * Retorno padrão para recursos não listados
 */
Route::any('{any}', function () {
    return response()->json([
        'status'=>'erro',
        'message'=>'Recurso não encontrado!'
    ], 404);
});
