<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\DataService;
use App\Http\Controllers\Api\V1\Concerns\SerializesDomainData;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelRoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use SerializesDomainData;

    /**
     * Create a booking.
     */
    public function store(Request $request, DataService $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hotel' => ['required', 'string'],
            'roomType' => ['required', 'string'],
            'paxes' => ['required', 'integer', 'min:1'],
            'checkin' => ['required', 'date_format:Y-m-d'],
            'checkout' => ['required', 'date_format:Y-m-d', 'after:checkin'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $hotel = $service->hotelByCode($data['hotel']);
        $roomType = $service->roomTypeByCode($data['roomType']);

        if ($hotel === null || $roomType === null) {
            return response()->json(['message' => 'The selected hotel or room type does not exist.'], 422);
        }

        $isAvailableAtHotel = $service->hotelRoomTypes()
            ->contains(fn (HotelRoomType $relation) => $relation->hotel->code === $hotel->code
                && $relation->roomType->code === $roomType->code);

        if (! $isAvailableAtHotel) {
            return response()->json(['message' => 'The selected room type is not available at the selected hotel.'], 422);
        }

        if ((int) $data['paxes'] > $roomType->max_occupancy) {
            return response()->json(['message' => 'The number of paxes exceeds the capacity of the selected room type.'], 422);
        }

        $booking = new Booking([
            'locator' => $this->generateLocator($service),
            'paxes' => (int) $data['paxes'],
            'checkin' => $data['checkin'],
            'checkout' => $data['checkout'],
            'status' => 'CONFIRMED',
        ]);
        $booking->hotel()->associate($hotel);
        $booking->roomType()->associate($roomType);

        $service->addBooking($booking);

        return response()->json($this->bookingPayload($booking), 201);
    }

    private function generateLocator(DataService $service): string
    {
        do {
            $locator = Str::upper(Str::random(6));
        } while ($service->bookings()->contains(fn (Booking $booking) => $booking->locator === $locator));

        return $locator;
    }
}
