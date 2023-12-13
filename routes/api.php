<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ApiController;
<<<<<<< HEAD
use App\Http\Controllers\ProductUploadController;
=======
use App\Http\Controllers\ProductImageUploadController;
>>>>>>> 5a3b292016e4383624978528fd336f7981233e39
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function() {
    Route::post('/phone/login', 'AuthController@phoneLogin');
    Route::post('/email/login', 'AuthController@emailLogin');
    Route::post('/phone/verify', 'AuthController@verifyPhone');
    Route::post('/register', 'AuthController@register');

    Route::get('/countries', 'LocationController@countries');
    Route::get('/regions', 'LocationController@regions');
    Route::get('/towns', 'LocationController@towns');
    Route::get('/streets', 'LocationController@streets');

    // Shop
    Route::get('/sub_categories', 'ShopController@getSubCategories');
    Route::get('/categories', 'ShopController@getCategories');
    Route::post('/shops', 'ShopController@store');
    Route::get('/shops', 'ShopController@index');

    Route::resource('products', 'ProductController', ['only' => ['index', 'store']]);
    Route::post('/product/delete', 'ProductController@deleteProduct');
    Route::get('/product/view', 'ProductController@view');
    Route::get('/products/related', 'ProductController@relatedProducts');

    Route::resource('errands', 'ErrandController', ['only' => ['index', 'store']]);
    Route::get('/errand/search', 'ErrandController@runErrand');
    Route::post('/errand/delete', 'ErrandController@deleteErrand');

    Route::resource('reviews', 'ReviewController');

    Route::get('/notifications', 'NotificationController@index');
    Route::get('/notifications/mark_as_read', 'NotificationController@markAllRead');
<<<<<<< HEAD
    Route::post('/products/{id}/images/upload', [ProductUploadController::class, 'uploadProductGallery']);
    Route::delete('/product/{id}/images/delete', [ProductUploadController::class, 'removeProductImage']);
=======
    Route::post('save_images/{id}', [ProductImageUploadController::class, 'uploadProductGallery']);
    Route::delete('remove_image/{product_id}/', [ProductImageUploadController::class, 'removeProductImage']);
>>>>>>> 5a3b292016e4383624978528fd336f7981233e39
});
