<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//common routes start

Route::post('/login', '\App\Http\Controllers\AuthController@login');
Route::post('/forgetPassword', '\App\Http\Controllers\AuthController@forgetpassword');
Route::post('/checktoken', '\App\Http\Controllers\AuthController@token_check');
Route::post('/resetPassword', '\App\Http\Controllers\AuthController@reset_password');
Route::get('/logout/{id}', 'App\Http\Controllers\AuthController@logout');


// common routes ends

/// admin Register
Route::post('/admin/register', 'App\Http\Controllers\Admin\AuthController@register');

/// seller Register
Route::post('/seller/register', 'App\Http\Controllers\Seller\AuthController@register');

/// customer Register
Route::post('/customer/register', 'App\Http\Controllers\Customer\AuthController@register');


Route::group(['middleware' => ['auth:api']], function(){


});  





       /////////////////////////////////// Admin Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

       Route::get('/admin/profile/view/{id}', 'App\Http\Controllers\Admin\AuthController@profile_view');
       Route::post('/admin/profile', 'App\Http\Controllers\Admin\AuthController@profile_update');
       Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
       Route::get('/admin/profile/check', 'App\Http\Controllers\Admin\AuthController@usercheck'); 
       Route::get('/admin/dashboard','App\Http\Controllers\Admin\DashboardController@index');



                                       /// Category \\\

        Route::group(['prefix' => '/admin/category/'], function() {
            Route::controller(App\Http\Controllers\Admin\CategoryController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });


                                               /// Brand \\\

        Route::group(['prefix' => '/admin/brand/'], function() {
            Route::controller(App\Http\Controllers\Admin\BrandController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });





                                        /// Product \\\

        Route::group(['prefix' => '/admin/product/'], function() {
            Route::controller(App\Http\Controllers\Admin\ProductController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });


                                        /// Package \\\

        Route::group(['prefix' => '/admin/package/'], function() {
            Route::controller(App\Http\Controllers\Admin\PackageController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });

        /////////////////////////////////// Seller Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

        Route::get('/seller/profile/view/{id}', 'App\Http\Controllers\Seller\AuthController@profile_view');
        Route::post('/seller/profile', 'App\Http\Controllers\Seller\AuthController@profile_update');
        Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
        Route::get('/seller/profile/check', 'App\Http\Controllers\Seller\AuthController@usercheck'); 
        Route::get('/seller/dashboard','App\Http\Controllers\Seller\DashboardController@index');



                                         /// Product \\\

        Route::group(['prefix' => '/customer/product/'], function() {
            Route::controller(App\Http\Controllers\Seller\ProductController::class)->group(function () {
                Route::get('show/{id}','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });



                                        /// Package \\\

        Route::group(['prefix' => '/customer/package/'], function() {
            Route::controller(App\Http\Controllers\Seller\PackageController::class)->group(function () {
                Route::get('show','index');
                Route::post('subscribe','subscribe');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
            });
        });

        /////////////////////////////////// Customer Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

        Route::get('/customer/profile/view/{id}', 'App\Http\Controllers\Customer\AuthController@profile_view');
        Route::post('/customer/profile', 'App\Http\Controllers\Customer\AuthController@profile_update');
        Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
        Route::get('/customer/profile/check', 'App\Http\Controllers\Customer\AuthController@usercheck'); 
        Route::get('/customer/dashboard','App\Http\Controllers\Customer\DashboardController@index');
