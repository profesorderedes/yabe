<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\SerializesMockData;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Services\MockDataService;
use Illuminate\Http\JsonResponse;

class RoomTypeController extends Controller
{
    use SerializesMockData;

    /**
     * List room types.
     */
    public function index(MockDataService $service): JsonResponse
    {
        return response()->json(
            $service->roomTypes()
                ->map(fn (RoomType $roomType) => $this->roomTypePayload($roomType))
                ->values()
        );
    }
}
