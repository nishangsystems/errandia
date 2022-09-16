<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('location')->group(function () {
    Route::get('/', 'LocationController@index');
});

Route::get('/region/town', [\Modules\Location\Http\Controllers\LocationController::class, 'getTownByRegion']);
Route::get('/town/street', [\Modules\Location\Http\Controllers\LocationController::class, 'getStreetByTown']);
