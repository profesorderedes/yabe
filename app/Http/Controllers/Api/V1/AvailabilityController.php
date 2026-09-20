<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\SerializesMockData;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelRoomType;
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
    public function check(Request $request, MockDataService $service): JsonResponse
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
        $checkin = $data['checkin'];
        $checkout = $data['checkout'];
        $paxes = (int) $data['paxes'];

        $available = $service->hotelRoomTypes()
            ->filter(fn (HotelRoomType $relation) => $relation->roomType->max_occupancy >= $paxes)
            ->filter(fn (HotelRoomType $relation) => is_null($data['hotel'] ?? null) || $relation->hotel->code === $data['hotel'])
            ->filter(fn (HotelRoomType $relation) => is_null($data['roomType'] ?? null) || $relation->roomType->code === $data['roomType'])
            ->filter(fn (HotelRoomType $relation) => $this->hasAvailableUnits($service, $relation, $checkin, $checkout))
            ->values()
            ->map(fn (HotelRoomType $relation) => [
                'hotel' => $this->hotelPayload($service, $relation->hotel),
                'roomType' => $this->roomTypePayload($relation->roomType),
                'price' => $relation->price,
            ]);

        return response()->json($available);
    }

    private function hasAvailableUnits(MockDataService $service, HotelRoomType $relation, string $checkin, string $checkout): bool
    {
        $overlapping = $service->bookings()
            ->filter(fn (Booking $booking) => $booking->status === 'CONFIRMED')
            ->filter(fn (Booking $booking) => $booking->hotel->code === $relation->hotel->code)
            ->filter(fn (Booking $booking) => $booking->roomType->code === $relation->roomType->code)
            ->filter(fn (Booking $booking) => $booking->checkin->format('Y-m-d') < $checkout
                && $booking->checkout->format('Y-m-d') > $checkin)
            ->count();

        return $relation->quantity - $overlapping >= 1;
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
