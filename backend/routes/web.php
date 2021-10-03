<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HttpErrorController;
use App\Http\Controllers\Api\Ride\MeetingPlaceController;
use App\Http\Controllers\Api\Ride\RideRouteController;
use App\Http\Controllers\Api\Ride\RideController;
use App\Http\Controllers\RideViewController;
use App\Http\Controllers\ParticipationController;

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

//常時

//GET
Route::get('/', [RideViewController::class, 'showHome'])->name('showHome');


//GET API
Route::get('api/get/rides/{time_appoint}/{prefecture_code}/{intensity}', [RideController::class, 'getRides'])->whereNumber('time_appoint', 'prefecture_code', 'intensityRange')->name('getRides');


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

    Route::get('api/get/rides/my-rides', [RideController::class, 'getRegisteredRides'])->name('getRegisteredRides');


    //POST API
    Route::post('api/post/meetingPlace', [MeetingPlaceController::class, 'createMeetingPlace'])->name('createMeetingPlace');

    Route::post('api/post/rideRoute', [RideRouteController::class, 'createRideRoute'])->name('createRideRoute');

    Route::post('api/post/createRide', [RideController::class, 'createRide'])->name('createRide');

    Route::post('api/post/participation', [ParticipationController::class, 'participationRegister'])->name('participationRegister');

    Route::post('api/post/participation/delete', [ParticipationController::class, 'cancelParticipation'])->name('cancelParticipationRegister');

    

});


//HTTP Error
Route::get('/register/ctrl', [HttpErrorController::class, 'methodNotAllowed']);
Route::get('/login/ctrl', [HttpErrorController::class, 'methodNotAllowed']);
