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
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('users',[UsersController::class,'index'])->name('users.list');
        Route::post('users/store',[UsersController::class,'store'])->name('users.store');
        Route::get('users/{id}',[UsersController::class,'show'])->where('id', '[0-9]+')->name('users.show');

        Route::put('users/update',[UsersController::class,'update'])->name('users.update');
        Route::delete('users/{id}',[UsersController::class,'destroy'])->where('id', '[0-9]+')->name('users.delete');

        Route::get('user/{id}/roles',[ApiController::class,'userRole'])->where('id', '[0-9]+')->name('get_user_role');
        Route::post('user/{id}/role',[ApiController::class,'assignRole'])->where('id', '[0-9]+')->name('assign.role');
        Route::delete('users/{id}/role/{role_id}',[ApiController::class,'removeUserRole'])->where('id', '[0-9]+')->where('role_id','[0-9]+')->name('remove.user_role');
    });
