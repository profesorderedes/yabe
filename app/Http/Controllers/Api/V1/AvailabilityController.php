<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\SerializesMockData;
use App\Http\Controllers\Controller;
use App\Models\HotelRoomType;
use App\Services\AvailabilityService;
use App\Services\MockDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    use SerializesMockData;

    /**
     * Check availability.
     */
    public function check(Request $request, AvailabilityService $availability, MockDataService $service): JsonResponse
    {
        $result = $this->validated($request, [
            'hotel' => ['nullable', 'string'],
            'roomType' => ['nullable', 'string'],
            'paxes' => ['required', 'integer', 'min:1'],
            'checkin' => ['required', 'date_format:Y-m-d'],
            'checkout' => ['required', 'date_format:Y-m-d', 'after:checkin'],
        ]);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        $data = $result;

        $available = $availability
            ->availableRoomTypes(
                paxes: (int) $data['paxes'],
                checkin: $data['checkin'],
                checkout: $data['checkout'],
                hotel: $data['hotel'] ?? null,
                roomType: $data['roomType'] ?? null,
            )
            ->map(fn (HotelRoomType $relation) => [
                'hotel' => $this->hotelPayload($service, $relation->hotel),
                'roomType' => $this->roomTypePayload($relation->roomType),
                'price' => $relation->price,
            ]);

        return response()->json($available);
    }

    /**
     * @param  array<string, array<int, string>>  $rules
     * @return array<string, mixed>|JsonResponse
     */
    private function validated(Request $request, array $rules): array|JsonResponse
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        return $validator->validated();
    }
}
