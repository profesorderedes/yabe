<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\DataService;
use App\Http\Controllers\Api\V1\Concerns\SerializesDomainData;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;

class RoomTypeController extends Controller
{
    use SerializesDomainData;

    /**
     * List room types.
     */
    public function index(DataService $service): JsonResponse
    {
        return response()->json(
            $service->roomTypes()
                ->map(fn (RoomType $roomType) => $this->roomTypePayload($roomType))
                ->values()
        );
    }
}
