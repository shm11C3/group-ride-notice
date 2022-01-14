<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HttpErrorController;
use App\Http\Controllers\Api\Ride\MeetingPlaceController;
use App\Http\Controllers\Api\Ride\RideRouteController;
use App\Http\Controllers\Api\Ride\RideController;
use App\Http\Controllers\Api\Ride\WeatherController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\User\FollowController;
use App\Http\Controllers\RideViewController;
use App\Http\Controllers\Api\ParticipationController;
use App\Http\Controllers\Api\StravaAuthController;
use App\Http\Controllers\Api\StravaController;
use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\SearchViewController;
use App\Http\Controllers\UserViewController;
use App\Http\Controllers\GoogleLoginController;

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

Route::get('/ride', [RideViewController::class, 'showRide'])->name('showRide');

Route::get('/user/{user_uuid}', [UserViewController::class, 'showUser'])->whereUuid('user_uuid')->name('showUser');

Route::get('/search', [SearchViewController::class, 'showSearch'])->name('showSearch');

Route::get('privacy-policy', function(){
    return view('auth.privacy');
})->name('policy');


// GET API

// 検索
Route::get('api/search/{keyword}/{option}', [SearchController::class, 'search'])->name('search');

// ライド関連
Route::get('api/get/rides/{time_appoint}/{prefecture_code}/{intensity}/{filterFollow}', [RideController::class, 'getRides'])->whereNumber('time_appoint', 'prefecture_code', 'intensityRange', 'filterFollow')->name('getRides');

Route::get('api/get/ride/{ride_uuid}', [RideController::class, 'getRideBy_rides_uuid'])->whereUuid('ride_uuid')->name('getRide');

Route::get('api/get/weather/{prefecture_code}', [WeatherController::class, 'getWeather'])->whereNumber('prefecture_code')->name('getWeather');

Route::get('api/get/meeting-places/{prefecture_code}', [MeetingPlaceController::class, 'getAllMeetingPlaces'])->whereNumber('prefecture_code')->name('getAllMeetingPlaces');

Route::get('api/get/ride-routes/{lap_status}', [RideRouteCOntroller::class, 'getAllRideRoutes'])->whereNumber('lap_status')->name('getAllRideRoutes');

// ユーザープロフィール関連
Route::get('api/get/profile/{user_uuid}', [UserProfileController::class, 'getUserProfile'])->whereUuid('user_uuid')->name('getUserProfile');

Route::get('api/get/userRides/{user_uuid}', [RideController::class, 'getUserRides'])->whereUuid('user_uuid')->name('getUserRides');

// フォロー関係
Route::get('api/get/follows/{user_by}', [FollowController::class, 'getFollows'])->whereUuid('user_by')->name('getFollows');

Route::get('api/get/followers/{user_to}', [FollowController::class, 'getFollowers'])->whereUuid('user_to')->name('getFollowers');

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

    Route::get('/my-ride', [RideViewController::class, 'showRideAdmin'])->name('rideAdmin');

    Route::get('user/config', [UserViewController::class, 'showConfig'])->name('showConfig');

    Route::get('user/config/password', [AuthController::class, 'showUpdatePassword'])->name('showUpdatePassword');

    Route::get('user/config/delete', [AuthController::class, 'showDeleteUser'])->name('showDeleteUser');

    Route::get('my-rides', [RideViewController::class, 'showMyRides'])->name('showMyRides');

    Route::get('meeting-place/register', [RideViewController::class, 'showMeetingPlaceRegisterForm'])->name('showMeetingPlaceRegisterForm');

    Route::get('ride-route/register', [RideViewController::class, 'showRegisterRideRouteForm'])->name('showRegisterRideRouteForm');

    Route::get('/auth/oAuthUser/register', [AuthController::class, 'showRegisterOAuthUser'])->name('showRegisterOAuthUser');

    Route::get('auth/oauth/error/isExist', function(){
        return view('auth.errors/isExist');
    })->name('showOAuthUserAlreadyRegistered');

    //POST
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('updatePassword', [AuthController::class, 'updatePassword'])->name('updatePassword');

    Route::post('deleteUser', [AuthController::class, 'deleteUser'])->name('deleteUser');

    Route::post('deleteOAuthUser', [AuthController::class, 'deleteOAuthUser'])->name('deleteOAuthUser');

    Route::post('/auth/oAuthUser/post/register', [AuthController::class, 'registerOAuthUser'])->name('registerOAuthUser');

    //GET API
    Route::get('api/get/savedMeetingPlaces', [MeetingPlaceController::class, 'getSavedMeetingPlaces'])->name('getSavedMeetingPlaces');

    Route::get('api/get/savedRideRoutes', [RideRouteController::class, 'getSavedRideRoutes'])->name('getSavedRideRoutes');

    Route::get('api/get/my-rides/{option}', [RideController::class, 'getRidesRelatedToAuthorizedUser'])->whereNumber('option')->name('getMyRides');

    Route::get('api/get/my-ride/{ride_uuid}', [RideController::class, 'getAuthorizedRideBy_rides_uuid'])->whereUuid('ride_uuid')->name('getAuthorizedRide');

    Route::get('api/get/my-profile', [UserProfileController::class, 'getAuthUserProfile'])->name('getAuthUserProfile');

    Route::get('api/get/strength', [StravaController::class, 'getUserStrength'])->name('getUserStrength');

    Route::get('api/strava/get/route/{page}', [StravaController::class, 'getUserRoute'])->whereNumber('page')->name('getUserRoute');


    //POST API
    Route::post('api/post/meetingPlace', [MeetingPlaceController::class, 'createMeetingPlace'])->name('createMeetingPlace');

    Route::post('api/post/rideRoute', [RideRouteController::class, 'createRideRoute'])->name('createRideRoute');

    Route::post('api/post/createRide', [RideController::class, 'createRide'])->name('createRide');

    Route::post('api/post/updateRide', [RideController::class, 'updateRide'])->name('updateRide');

    Route::post('api/post/updatePublishStatus', [RideController::class, 'updatePublishStatus'])->name('UpdatePublishStatus');

    Route::post('api/post/participation', [ParticipationController::class, 'participationRegister'])->name('participationRegister');

    Route::post('api/post/participation/delete', [ParticipationController::class, 'cancelParticipation'])->name('cancelParticipationRegister');

    Route::post('api/post/profile/update', [UserProfileController::class, 'updateUserProfile'])->name('updateUserProfile');

    Route::post('api/post/registerMeetingPlace', [MeetingPlaceController::class, 'registerMeetingPlace'])->name('registerMeetingPlace');

    Route::post('api/post/registerRideRoute', [RideRouteController::class, 'registerMeetingPlace'])->name('registerRideRoute');

    Route::post('api/post/follow', [FollowController::class, 'follow'])->name('follow');

    Route::post('api/post/upload/userProfileImg', [UserProfileController::class, 'uploadUserProfileImg'])->name('uploadUserProfileImg');

    Route::post('api/post/delete/userProfileImg', [UserProfileController::class, 'deleteUserProfileImg'])->name('deleteUserProfileImg');
});


//HTTP Error
Route::get('/register/ctrl', [HttpErrorController::class, 'methodNotAllowed']);
Route::get('/login/ctrl', [HttpErrorController::class, 'methodNotAllowed']);

// Google Auth
Route::get('/auth/redirect', [GoogleLoginController::class, 'getGoogleAuth'])->name('googleAuth');
Route::get('/login/callback', [GoogleLoginController::class, 'authGoogleCallback']);

// Strava Auth
Route::get('strava/oauth/redirect', [StravaAuthController::class, 'stravaAuth'])->name('stravaAuth');
Route::get('strava/oauth/callback', [StravaAuthController::class, 'authStravaCallback']);
