<?php

namespace App\Contracts;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use Illuminate\Support\Collection;

interface DataService
{
    /**
     * @return Collection<int, Hotel>
     */
    public function hotels(): Collection;

    /**
     * @return Collection<int, RoomType>
     */
    public function roomTypes(): Collection;

    /**
     * @return Collection<int, HotelRoomType>
     */
    public function hotelRoomTypes(): Collection;

    /**
     * @return Collection<int, Booking>
     */
    public function bookings(): Collection;

    public function addBooking(Booking $booking): void;

    public function hotelByCode(string $code): ?Hotel;

    public function roomTypeByCode(string $code): ?RoomType;
}
