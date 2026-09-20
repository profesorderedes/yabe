<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\SerializesMockData;
use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\MockDataService;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    use SerializesMockData;

    /**
     * List hotels.
     */
    public function index(MockDataService $service): JsonResponse
    {
        return response()->json(
            $service->hotels()
                ->map(fn (Hotel $hotel) => $this->hotelPayload($service, $hotel))
                ->values()
        );
    }
}
