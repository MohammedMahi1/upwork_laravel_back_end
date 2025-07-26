<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('guest:sanctum')->group(function () {

    //Authentication logic
    Route::post("/user/register", [UserController::class, "register"]);
    Route::post("/user/login", [UserController::class, "login"]);



    //Reset password


});
    Route::post('/user/forgot-password', [PasswordResetController::class, 'sendResetPasswordLink']);
    Route::post('/user/reset-password', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {

    //Logout auth
    Route::delete('/user/logout/{token?}', [UserController::class, "logout"]);
    
    //OTP mail verification
    Route::delete('/otp/cancel-otp-creating/{token?}', [UserController::class, "cancelOtpAction"]);
    Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);
    Route::post('/otp/send', [OtpController::class, 'sendOtp']);

    //User profile
    Route::put('/user/update/profile', [UserController::class, "updateProfile"]);
    Route::put('/user/update/password', [UserController::class, "updatePassword"]);
    Route::get('/user', [UserController::class, "index"]);
    Route::get('/test', [UserController::class, "test"]);

});
