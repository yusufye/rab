<?php

use App\Http\Controllers\pages\Page2;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\authentications\RegisterBasic;

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

// Main Page Route
Route::get('/', [HomePage::class, 'index'])->name('pages-home');
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

// locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// pages
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

// order
Route::get('/order', [OrderController::class, 'index'])->name('order');
Route::get('/order/create', [OrderController::class, 'create'])->name('order');
Route::post('/order/submit', [OrderController::class, 'store']);
Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('order');
Route::post('/order/{order}/update', [OrderController::class, 'update']);
Route::post('/order/mak/submit', [OrderController::class, 'storeMak']);
Route::post('/order/mak/delete', [OrderController::class, 'deleteMak']);
Route::post('/order/title/submit', [OrderController::class, 'storeTitle']);
Route::post('/order/title/delete', [OrderController::class, 'deleteTitle']);
Route::post('/order/item/submit', [OrderController::class, 'storeItem']);
Route::post('/order/item/delete', [OrderController::class, 'deleteItem']);

//mak
Route::get('/mak', [MakController::class, 'index'])->name('mak');
Route::get('/mak/create', [MakController::class, 'create'])->name('mak');
Route::post('/mak/submit', [MakController::class, 'store']);
Route::get('/mak/{mak}/edit', [MakController::class, 'edit'])->name('mak');
Route::post('/mak/{mak}/update', [MakController::class, 'update']);