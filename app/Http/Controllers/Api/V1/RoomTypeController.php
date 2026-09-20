<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RoomTypeController extends Controller
{
    /**
     * List room types.
     */
    public function index(): JsonResponse
    {
        return response()->json([], 200);
    }
}
