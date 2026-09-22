<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Contracts\DataService;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;

trait SerializesDomainData
{
    /**
     * @return array<string, mixed>
     */
    private function roomTypePayload(RoomType $roomType): array
    {
        return [
            'code' => $roomType->code,
            'name' => $roomType->name,
            'maxOccupancy' => $roomType->max_occupancy,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function hotelPayload(DataService $service, Hotel $hotel): array
    {
        return [
            'name' => $hotel->name,
            'code' => $hotel->code,
            'roomTypes' => $service->hotelRoomTypes()
                ->filter(fn (HotelRoomType $relation) => $relation->hotel->code === $hotel->code)
                ->map(fn (HotelRoomType $relation) => $this->hotelRoomTypePayload($relation))
                ->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function hotelRoomTypePayload(HotelRoomType $relation): array
    {
        return [
            'roomType' => $this->roomTypePayload($relation->roomType),
            'quantity' => $relation->quantity,
            'price' => $relation->price,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingPayload(Booking $booking): array
    {
        return [
            'locator' => $booking->locator,
            'hotel' => $booking->hotel->code,
            'roomType' => $booking->roomType->code,
            'paxes' => $booking->paxes,
            'checkin' => $booking->checkin->format('Y-m-d'),
            'checkout' => $booking->checkout->format('Y-m-d'),
            'status' => $booking->status,
        ];
    }
}
