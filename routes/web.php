<?php

use App\Http\Controllers\PlansController;
use App\Http\Controllers\SettingsController;
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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth', 'redirectIfHasntFilledCreditCard']], function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/paymentMethod/delete', [SettingsController::class, 'deletePaymentMethod'])->name('settings.deletePaymentMethod');
    Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
    Route::post('/plans', [PlansController::class, 'store'])->name('plans.store');
    Route::delete('/plans/delete', [PlansController::class, 'destroy'])->name('plans.destroy');
    Route::post('/plans/subscribe', [PlansController::class, 'subscribe'])->name('plans.subscribe');
    Route::post('/plans/cancel', [PlansController::class, 'cancelSubscription'])->name('subscription.cancel');
});
