<?php

use App\Http\Controllers\pages\Page2;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\UserController;

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

// locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// pages
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

Route::middleware(['auth', 'verified'])->group(function () {

    // Main Page Route
    Route::get('/', [HomePage::class, 'index'])->name('pages-home')->middleware('menu.permission:read_home');
    Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

    // order
    Route::get('/order', [OrderController::class, 'index'])->name('order')->middleware('menu.permission:read_order');
    Route::get('/order/create', [OrderController::class, 'create'])->name('order')->middleware('menu.permission:create_order');
    Route::post('/order/submit', [OrderController::class, 'store']);
    Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('order')->middleware('menu.permission:update_order');
    Route::get('/order/{order}', [OrderController::class, 'view'])->name('order')->middleware('menu.permission:read_order');
    Route::post('/order/{order}/update', [OrderController::class, 'update']);
    Route::post('/order/mak/submit', [OrderController::class, 'storeMak']);
    Route::post('/order/mak/delete', [OrderController::class, 'deleteMak']);
    Route::post('/order/title/submit', [OrderController::class, 'storeTitle']);
    Route::post('/order/title/delete', [OrderController::class, 'deleteTitle']);
    Route::post('/order/item/submit', [OrderController::class, 'storeItem']);
    Route::post('/order/item/delete', [OrderController::class, 'deleteItem']);
    Route::get('/order/{order}/revise', [OrderController::class, 'revise']);
    Route::post('/order/update_status/submit', [OrderController::class, 'updateStatus']);
    Route::get('/order/get_cheklist/{order_item_id}', [OrderController::class, 'getChecklist']);
    Route::post('/order/save_checklist/{order_item_id}', [OrderController::class, 'saveChecklist']);
    Route::get('/order/{order}/download/{type?}', [OrderController::class, 'download'])->name('orderDownload');
    Route::get('/order/{order}/get_divisions', [OrderController::class, 'getDivisions']);
    Route::get('/get_order_mak/{order_mak}', [OrderController::class, 'getOrderMak']);
    Route::get('/get_order_title/{id}', [OrderController::class, 'getOrderTitle']);
    Route::get('/get_order_item/{id}', [OrderController::class, 'getOrderItem']);



    //master mak
    Route::get('/mak', [MakController::class, 'index'])->name('mak')->middleware('menu.permission:read_mak');
    Route::get('/mak/create', [MakController::class, 'create'])->name('mak')->middleware('menu.permission:create_mak');
    Route::post('/mak/submit', [MakController::class, 'store'])->name('mak')->middleware('menu.permission:create_mak');
    Route::get('/mak/{mak}/edit', [MakController::class, 'edit'])->name('mak')->middleware('menu.permission:update_mak');
    Route::post('/mak/{mak}/update', [MakController::class, 'update']);

    //master category
    Route::get('/category', [CategoryController::class, 'index'])->name('category')->middleware('menu.permission:read_category');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category')->middleware('menu.permission:create_category');
    Route::post('/category/submit', [CategoryController::class, 'store']);
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category')->middleware('menu.permission:update_category');
    Route::post('/category/{category}/update', [CategoryController::class, 'update']);

    //report
    Route::get('/report/filter/{type?}', [ReportController::class, 'filter'])->name('report_filter')->middleware('menu.permission:read_report');
    Route::post('/report/show/{type?}', [ReportController::class, 'show'])->name('report_show');


    // permission
    Route::get('/roles-and-permission', [UserController::class, 'indexRole'])->name('role-and-permission')->middleware('menu.permission:read_role_&_permission');
    Route::get('/ajax_list_users', [UserController::class, 'ajax_list_users'])->name('ajax_list_users');
    Route::post('/add/role', [UserController::class, 'add_roles'])->middleware('menu.permission:create_role_&_permission');
    Route::post('/edit/role', [UserController::class, 'edit_roles'])->middleware('menu.permission:update_role_&_permission');
    Route::get('/user', [UserController::class, 'index'])->name('user')->middleware('menu.permission:read_user');
    Route::get('/user/create', [UserController::class, 'create'])->name('user')->middleware('menu.permission:create_user');
    Route::post('/user/submit', [UserController::class, 'store'])->middleware('menu.permission:create_user');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user')->middleware('menu.permission:update_user');
    Route::post('/user/{user}/update', [UserController::class, 'update'])->name('user')->middleware('menu.permission:update_user');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});