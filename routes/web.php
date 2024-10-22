<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/testing', function () {
    return view('testing');
});

Route::get('/email/track/open/{batchId}', [App\Http\Controllers\Admin\SubscriberController::class, 'trackEmailOpen'])->name('email.track.open');



