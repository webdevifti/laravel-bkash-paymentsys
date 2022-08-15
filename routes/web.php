<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});


Route::resource('/orders', OrderController::class);
Route::post('/token', [PaymentController::class, 'token'])->name('token');

Route::get('/create-payment', [PaymentController::class,'createPayment'])->name('createpayment');
Route::get('/excute-payment', [PaymentController::class,'excutePayment'])->name('executepayment');