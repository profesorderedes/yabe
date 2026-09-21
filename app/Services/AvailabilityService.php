<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HotelRoomType;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function __construct(private readonly MockDataService $data) {}

    /**
     * Find the hotel room types that are available for the given criteria.
     *
     * @return Collection<int, HotelRoomType>
     */
    public function availableRoomTypes(
        int $paxes,
        string $checkin,
        string $checkout,
        ?string $hotel = null,
        ?string $roomType = null,
    ): Collection {
        return $this->data->hotelRoomTypes()
            ->filter(fn (HotelRoomType $relation) => $this->fitsCapacity($relation, $paxes))
            ->filter(fn (HotelRoomType $relation) => $this->matchesFilters($relation, $hotel, $roomType))
            ->filter(fn (HotelRoomType $relation) => $this->hasFreeUnits($relation, $checkin, $checkout))
            ->values();
    }

    private function fitsCapacity(HotelRoomType $relation, int $paxes): bool
    {
        return $relation->roomType->max_occupancy >= $paxes;
    }

    private function matchesFilters(HotelRoomType $relation, ?string $hotel, ?string $roomType): bool
    {
        return ($hotel === null || $relation->hotel->code === $hotel)
            && ($roomType === null || $relation->roomType->code === $roomType);
    }

    private function hasFreeUnits(HotelRoomType $relation, string $checkin, string $checkout): bool
    {
        return $relation->quantity > $this->occupiedUnits($relation, $checkin, $checkout);
    }

    private function occupiedUnits(HotelRoomType $relation, string $checkin, string $checkout): int
    {
        return $this->data->bookings()
            ->filter(fn (Booking $booking) => $booking->status === 'CONFIRMED')
            ->filter(fn (Booking $booking) => $booking->hotel->code === $relation->hotel->code)
            ->filter(fn (Booking $booking) => $booking->roomType->code === $relation->roomType->code)
            ->filter(fn (Booking $booking) => $booking->checkin->format('Y-m-d') < $checkout
                && $booking->checkout->format('Y-m-d') > $checkin)
            ->count();
    }
}
