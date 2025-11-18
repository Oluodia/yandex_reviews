<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\ReviewsController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/registration', [RegistrationController::class, 'index'])->name('register');

Route::post('/login', [LoginController::class, 'store']);
Route::post('/registration', [RegistrationController::class, 'store']);



Route::middleware('auth')->group(function () {
	Route::get('/', [ReviewsController::class, 'reviews'])->name('reviews');

	Route::get('/settings', [ReviewsController::class, 'settings'])->name('settings.index');
	Route::post('/settings', [ReviewsController::class, 'setUrl'])->name('settings.store');

	Route::get('/logout', [LoginController::class, 'logout']);
});