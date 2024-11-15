<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\API')
    ->prefix('/v1')
    ->group(static function () {

        Route::post('/token', 'TokenGeneratorController@generateToken')
            ->name('api.token.new');

        Route::middleware('auth:sanctum')->group(static function () {
            Route::prefix('/historical')->group(static function () {
                Route::get('/', 'HistoricalDataController@getHistoricalData')
                    ->name('api.historical.get');
            });

            Route::prefix('/price-action')->group(static function () {
                Route::get('/all', 'PriceActionController@listAllPriceActions')
                    ->name('api.price-action.get-all');
                Route::get('/{entry_id}', 'PriceActionController@getSinglePriceAction')
                    ->name('api.price-action.get-one');

                Route::put('/{entry_id}/activate', 'PriceActionController@activatePriceAction')
                    ->name('api.price-action.activate');
                Route::put('/{entry_id}/deactivate', 'PriceActionController@deactivatePriceAction')
                    ->name('api.price-action.deactivate');

                Route::post('/', 'PriceActionController@addNewPriceAction')
                    ->name('api.price-action.add');

                Route::delete('/{entry_id}', 'PriceActionController@deletePriceAction')
                    ->name('api.price-action.delete');
            });

            Route::prefix('/percent-delta')->group(static function () {
                Route::get('/all', 'PercentDeltaController@listAllPercentDeltas')
                    ->name('api.percent-delta.get-all');
                Route::get('/{entry_id}', 'PercentDeltaController@getSinglePercentDelta')
                    ->name('api.percent-delta.get-one');

                Route::put('/{entry_id}/activate', 'PercentDeltaController@activatePercentDelta')
                    ->name('api.percent-delta.activate');
                Route::put('/{entry_id}/deactivate', 'PercentDeltaController@deactivatePercentDelta')
                    ->name('api.percent-delta.deactivate');

                Route::post('/', 'PercentDeltaController@addNewPercentDelta')
                    ->name('api.percent-delta.add');

                Route::delete('/{entry_id}', 'PercentDeltaController@deletePercentDelta')
                    ->name('api.percent-delta.delete');
            });
        });
    });
