<?php

use App\Http\Controllers\Admincontroller;
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
Route::get('/admin/login', [Admincontroller::class, 'indexLogin']);
Route::post('/admin/logout', [Admincontroller::class, 'logout'])->name('logout');
Route::post('/admin/submit-login', [AdminController::class, 'submitLogin']);
Route::group(['prefix' => '/admin', 'middleware' => 'checkLoginAdmin'], function () {
    Route::get('/get', [Admincontroller::class, 'getAdmin']);
    Route::post('/update-admin', [Admincontroller::class, 'updateAdmin'])->name('updateAdmin');
});
