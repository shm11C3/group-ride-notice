<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

//登録画面
Route::get('/register', [AuthController::class,'showRegister'])->name('showRegister');

//登録処理
Route::post('/register/ctrl', [AuthController::class,'store'])->name('register');
