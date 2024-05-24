<?php

use App\Http\Controllers\PaymentController;
use App\Models\User;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments', PaymentController::class)->middleware('auth')->name('payments.generate');
Route::get('/destroy-payments', [PaymentController::class, 'destroy'])->middleware('auth')->name('payments.delete');
Route::get('/payments/{payment}', [PaymentController::class, 'generatePDF'])->middleware('auth')->name('payments.pdf');
