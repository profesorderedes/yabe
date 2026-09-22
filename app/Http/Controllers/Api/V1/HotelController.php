<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\DataService;
use App\Http\Controllers\Api\V1\Concerns\SerializesDomainData;
use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    use SerializesDomainData;

    /**
     * List hotels.
     */
    public function index(DataService $service): JsonResponse
    {
        return response()->json(
            $service->hotels()
                ->map(fn (Hotel $hotel) => $this->hotelPayload($service, $hotel))
                ->values()
        );
    }
}
