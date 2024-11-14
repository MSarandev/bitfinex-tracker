<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\API')
    ->prefix('/v1')
    ->group(static function () {

        Route::post('/token', 'TokenGeneratorController@generateToken')->name('api.token.new');

        Route::middleware('auth:sanctum')->group(static function () {
            Route::get('/test', static function () {
                response()->json(['message' => 'Hello World!']);
            });

            Route::prefix('/historical')->group(static function () {
                Route::get('/', 'HistoricalDataController@getHistoricalData')->name('api.historical.get');
            });
        });
    });
