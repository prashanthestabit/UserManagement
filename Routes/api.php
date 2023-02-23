<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\ApiController;
use Modules\UserManagement\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    Route::post('login',[ApiController::class,'authenticate'])->name('auth.login');
    Route::post('register',[ApiController::class,'register'])->name('auth.register');
    Route::get('forgot-password',[ApiController::class,'forgotPassword'])->name('auth.forget_password');

    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('logout', [ApiController::class, 'logout'])->name('auth.logout');
        Route::get('user_profile',[ApiController::class,'getUser'])->name('auth.get_user');
        Route::put('update_user_profile',[ApiController::class,'updateUser'])->name('auth.update_user');
        Route::post('change-password',[ApiController::class,'changePassword'])->name('auth.change_password');
        // User Management Routes

        // Route::resource('users',UsersController::class);
        // Route::apiResource('users',\Modules\UserManagement\Http\Controllers\UsersController::class);
        Route::get('users',[UsersController::class,'index']);
        Route::post('users/store',[UsersController::class,'store']);
        Route::get('users/{id}',[UsersController::class,'show']);

        Route::put('users/update',[UsersController::class,'update']);
        Route::delete('users/{id}',[UsersController::class,'destroy']);

        Route::get('user/{id}/roles',[ApiController::class,'userRole'])->name('get_user_role');
        Route::post('user/{id}/role',[ApiController::class,'assignRole'])->name('assign.role');
        Route::delete('users/{id}/role/{role_id}',[ApiController::class,'removeUserRole'])->name('remove.user_role');
    });
