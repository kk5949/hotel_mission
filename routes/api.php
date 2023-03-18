<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hotel\HotelController;
use App\Http\Controllers\Reservation\ReservationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user/login', [AuthController::class, 'userLogin'])->name('user.login');
Route::post('/staff/login', [AuthController::class, 'staffLogin'])->name('staff.login');
Route::post('/user', [AuthController::class, 'store'])->name('user.store');



// Sanctum 미들웨어 적용된 라우터 그룹
Route::middleware('auth:sanctum')->group(function () {
    // 로그아웃
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource("hotel",HotelController::class);
    Route::apiResource("reservation",ReservationController::class);

    // 예약 취소,반려,승인
    Route::patch("/reservation/{reservation}/cancel",[ReservationController::class,"cancel"])->name("reservation.cancel");
    Route::patch("/reservation/{reservation}/reject",[ReservationController::class,"reject"])->name("reservation.reject");
    Route::patch("/reservation/{reservation}/confirm",[ReservationController::class,"confirm"])->name("reservation.confirm");
});

