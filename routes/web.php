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

Route::get('/admin/login', [Admincontroller::class, 'indexLogin']);
Route::post('/admin/logout', [Admincontroller::class, 'logout'])->name('logout');
Route::post('/admin/submit-login', [AdminController::class, 'submitLogin']);

// quản lý admin
Route::group(['prefix' => '/admin', 'middleware' => 'checkLoginAdmin'], function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/get', [Admincontroller::class, 'getAdmin'])->name('getAdmin');
    Route::post('/update-admin', [Admincontroller::class, 'updateAdmin'])->name('updateAdmin');
    Route::post('/check-add-admin-via-file', [AdminController::class, 'check_add_admin_via_file'])->name('admin.check_add_admin_via_file');
    Route::get('/index', [AdminController::class, 'indexAdmin']);
    Route::post('/create-admin', [AdminController::class, 'createAdmin'])->name('createAdmin');
    Route::delete('/delete-admin', [AdminController::class, 'deleteAdmin'])->name('deleteAdmin');
    Route::put('/update-admin', [AdminController::class, 'updateAdmin'])->name('updateAdmin');

    Route::group(['prefix' => '/truongbomon', 'middleware' => 'checkLoginAdmin'], function () {
        Route::get('/', [AdminController::class, 'indexTestFile'])->name('testFile');
        Route::get('/get', [Admincontroller::class, 'getTBM'])->name('getTBM');
        Route::post('/update-tbm', [Admincontroller::class, 'updateTBM'])->name('updateTBM');
        Route::post('/check-add-tbm-via-file', [AdminController::class, 'check_add_tbm_via_file'])->name('check_add_tbm_via_file');
        Route::get('/indexTBM', [AdminController::class, 'indexTBM']);
        Route::post('/create-tbm', [AdminController::class, 'createTBM'])->name('createTBM');
        Route::delete('/delete-tbm', [AdminController::class, 'deleteTBM'])->name('deleteTBM');
        Route::put('/update-tbm', [AdminController::class, 'updateTBM'])->name('updateTBM');
    });
});

//quản lý trưởng bộ môn
