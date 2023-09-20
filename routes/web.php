<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

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

Route::get('/register',[AuthController::class,'loadRegister']);
Route::post('/register',[AuthController::class,'studentRegister'])->name('studentRegister');

Route::get('/login',function(){
    return redirect('/');
});

Route::get('/',[AuthController::class,'loadLogin']);
Route::post('/login',[AuthController::class,'userLogin'])->name('userLogin');

Route::get('/logout',[AuthController::class,'logout']);

Route::get('/forget-password',[AuthController::class,'forgetPasswordload']);
Route::post('/forget-password',[AuthController::class,'forgetPassword'])->name('forgetPassword');

Route::get('/reset-password',[AuthController::class,'resetPasswordload']);
Route::post('/reset-password',[AuthController::class,'resetPassword'])->name('resetPassword');

Route::group(['middleware'=>['web','checkAdmin']],function(){
    Route::get('/admin/dashboard',[AuthController::class,'admindashboard']);

    // subjects route
    Route::post('/add-subject',[AdminController::class,'addSubject'])->name('addSubject');
    Route::post('/edit-subject',[AdminController::class,'editSubject'])->name('editSubject');
    Route::post('/delete-subject',[AdminController::class,'deleteSubject'])->name('deleteSubject');
});

Route::group(['middleware'=>['web','checkStudent']],function(){
    Route::get('/dashboard',[AuthController::class,'loaddashboard']);
});

