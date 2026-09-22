<?php

namespace App\Services;

use App\Contracts\DataService;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use Illuminate\Support\Collection;

class EloquentDataService implements DataService
{
    /**
     * @return Collection<int, Hotel>
     */
    public function hotels(): Collection
    {
        return Hotel::all();
    }

    /**
     * @return Collection<int, RoomType>
     */
    public function roomTypes(): Collection
    {
        return RoomType::all();
    }

    /**
     * @return Collection<int, HotelRoomType>
     */
    public function hotelRoomTypes(): Collection
    {
        return HotelRoomType::with(['hotel', 'roomType'])->get();
    }

    /**
     * @return Collection<int, Booking>
     */
    public function bookings(): Collection
    {
        return Booking::with(['hotel', 'roomType'])->get();
    }

    public function addBooking(Booking $booking): void
    {
        $booking->save();
    }

    public function hotelByCode(string $code): ?Hotel
    {
        return Hotel::where('code', $code)->first();
    }

    public function roomTypeByCode(string $code): ?RoomType
    {
        return RoomType::where('code', $code)->first();
    }
}
