<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    /**
     * List hotels.
     */
    public function index(): JsonResponse
    {
        return response()->json([], 200);
    }
}
