<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HttpErrorController;
use App\Http\Controllers\Api\Ride\MeetingPlaceController;
use App\Http\Controllers\Api\Ride\RideRouteController;
use App\Http\Controllers\Api\Ride\RideController;
use App\Http\Controllers\RideViewController;

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


//非ログイン時
Route::group(['middleware' => ['guest']], function () {

    //GET
    Route::get('/register', [AuthController::class,'showRegister'])->name('showRegister'); //登録画面

    Route::get('/login', [AuthController::class, 'showLogin'])->name('showLogin'); //ログイン画面


    //POST
    Route::post('/register/ctrl', [AuthController::class,'store'])->name('register'); //登録・認証処理

    Route::post('/login/ctrl', [AuthController::class, 'login'])->name('login'); //認証処理

});

//ログイン時
Route::group(['middleware' => ['auth']], function () {
    
    //GET
    Route::get('/dashboard', [AuthController::class, 'showDashboard'])->name('showDashboard'); //ダッシュボード

    Route::get('/create-ride', [RideViewController::class, 'showRideForm'])->name('createRideForm'); 


    //POST
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');


    //GET API
    Route::get('api/get/savedMeetingPlaces', [MeetingPlaceController::class, 'getSavedMeetingPlaces'])->name('getSavedMeetingPlaces');

    Route::get('api/get/savedRideRoutes', [RideRouteController::class, 'getSavedRideRoutes'])->name('getSavedMeetingPlaces');


    //POST API
    Route::post('api/post/meetingPlace', [MeetingPlaceController::class, 'createMeetingPlace'])->name('createMeetingPlace');

    Route::post('api/post/rideRoute', [RideRouteController::class, 'createRideRoute'])->name('createRideRoute');

    Route::post('api/post/createRide', [RideController::class, 'createRide'])->name('createRide');

});


//HTTP Error
Route::get('/register/ctrl', [HttpErrorController::class, 'methodNotAllowed']);
Route::get('/login/ctrl', [HttpErrorController::class, 'methodNotAllowed']);
