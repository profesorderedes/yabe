<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Create a booking.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([], 201);
    }
}
