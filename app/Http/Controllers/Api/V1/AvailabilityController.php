<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Check availability.
     */
    public function check(Request $request): JsonResponse
    {
        return response()->json([], 200);
    }
}
