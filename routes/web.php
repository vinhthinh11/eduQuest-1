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
Route::group(['prefix' => '/admin'], function () {
    Route::get('/get', [Admincontroller::class, 'getAdmin']);
    Route::get('/login', [Admincontroller::class, 'indexLogin']);
    Route::post('/submit-login', [AdminController::class, 'submitLogin']);
    Route::post('/logout', [Admincontroller::class, 'logout'])->name('logout');
    Route::post('/update-admin', [Admincontroller::class, 'updateAdmin'])->name('updateAdmin');
});
