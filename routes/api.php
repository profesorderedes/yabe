<?php

use App\Http\Controllers\Api\V1\AvailabilityController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\RoomTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/room-types', [RoomTypeController::class, 'index']);
Route::post('/availability', [AvailabilityController::class, 'check']);
Route::post('/bookings', [BookingController::class, 'store']);
