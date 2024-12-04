<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.', 'middleware' => ['api', 'throttle']], function ($router) {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function ($router) {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');
        Route::get('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
        Route::get('me', [AuthController::class, 'userDetail'])->middleware('auth:api')->name('user-detail');
    });

    Route::group(['prefix' => 'management-users', 'as' => 'management-users.'], function ($router) {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/roles', [UserController::class, 'listRole'])->name('roles');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/update/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/restore/{user}', [UserController::class, 'restore'])->name('restore');
    });

});
