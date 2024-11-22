<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tabby\TabyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('tabby_form', function () {
    // dd('okkk');
    return view('tabby-form');
})->name('tabby.form');

Route::post('/tabby/create-session', [TabyController::class, 'createSession'])->name('tabby.create-session');
// In custome
Route::get('/payment-error', [TabyController::class, 'showError'])->name('payment.error');

Route::post('/tabby/session', [TabyController::class, 'createSession']);
Route::get('/tabby/success', [TabyController::class, 'successCallback'])->name('tabby.success');
Route::get('/tabby/cancel', [TabyController::class, 'cancelCallback'])->name('tabby.cancel');
Route::get('/tabby/failure', [TabyController::class, 'failureCallback'])->name('tabby.failure');

Route::get('/order/success', function () {
    return view('order.success'); // Create a `success.blade.php` in the `order` folder inside `resources/views`
})->name('order.success');
Route::get('/order/cancel', function () {
    return view('order.cancel'); // Create a `cancel.blade.php` in the `order` folder
})->name('order.cancel');

Route::get('/order/failure', function () {
    return view('order.failure'); // Create a `failure.blade.php` in the `order` folder
})->name('order.failure');



