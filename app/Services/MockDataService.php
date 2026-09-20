<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use Illuminate\Support\Collection;

class MockDataService
{
    /**
     * @var Collection<int, Hotel>
     */
    private Collection $hotels;

    /**
     * @var Collection<int, RoomType>
     */
    private Collection $roomTypes;

    /**
     * @var Collection<int, HotelRoomType>
     */
    private Collection $hotelRoomTypes;

    /**
     * @var Collection<int, Booking>
     */
    private Collection $bookings;

    public function __construct()
    {
        $this->hotels = new Collection;
        $this->roomTypes = new Collection;
        $this->hotelRoomTypes = new Collection;
        $this->bookings = new Collection;

        $this->seed();
    }

    /**
     * Build the in-memory mock data.
     */
    private function seed(): void
    {
        $grand = $this->hotel('Grand Hotel', 'GRAND');
        $panorama = $this->hotel('Panorama View', 'PANORAMA');
        $delfi = $this->hotel('Delfi Suites', 'DELFI');

        $standard = $this->roomType('Standard Room', 'STANDARD', 2);
        $deluxe = $this->roomType('Deluxe Room', 'DELUXE', 2);
        $suite = $this->roomType('Suite', 'SUITE', 3);
        $family = $this->roomType('Family Room', 'FAMILY', 4);

        $this->inventory($grand, $standard, 20, 85.50);
        $this->inventory($grand, $deluxe, 12, 125.50);
        $this->inventory($grand, $suite, 5, 210.00);

        $this->inventory($panorama, $standard, 15, 95.00);
        $this->inventory($panorama, $suite, 3, 240.00);
        $this->inventory($panorama, $family, 6, 150.00);

        $this->inventory($delfi, $deluxe, 10, 140.00);
        $this->inventory($delfi, $family, 8, 175.00);

        $this->booking('ABC123', $grand, $deluxe, 2, '2026-10-01', '2026-10-05', 'CONFIRMED');
        $this->booking('XYZ789', $grand, $deluxe, 2, '2026-10-03', '2026-10-08', 'CONFIRMED');
        $this->booking('MNO456', $grand, $standard, 1, '2026-11-10', '2026-11-12', 'CONFIRMED');
        $this->booking('RTY369', $grand, $suite, 3, '2026-12-10', '2026-12-14', 'CONFIRMED');
        $this->booking('JKL321', $panorama, $family, 4, '2026-10-15', '2026-10-20', 'CONFIRMED');
        $this->booking('QWE654', $panorama, $standard, 1, '2026-12-01', '2026-12-04', 'CANCELLED');
        $this->booking('ASD987', $delfi, $deluxe, 2, '2026-09-25', '2026-09-28', 'CONFIRMED');
        $this->booking('ZXC741', $delfi, $family, 3, '2026-11-20', '2026-11-25', 'CONFIRMED');
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function hotels(): Collection
    {
        return $this->hotels;
    }

    /**
     * @return Collection<int, RoomType>
     */
    public function roomTypes(): Collection
    {
        return $this->roomTypes;
    }

    /**
     * @return Collection<int, HotelRoomType>
     */
    public function hotelRoomTypes(): Collection
    {
        return $this->hotelRoomTypes;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function bookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): void
    {
        $this->bookings->push($booking);
    }

    public function hotelByCode(string $code): ?Hotel
    {
        return $this->hotels->firstWhere('code', $code);
    }

    public function roomTypeByCode(string $code): ?RoomType
    {
        return $this->roomTypes->firstWhere('code', $code);
    }

    private function hotel(string $name, string $code): Hotel
    {
        $hotel = new Hotel(['name' => $name, 'code' => $code]);
        $this->hotels->push($hotel);

        return $hotel;
    }

    private function roomType(string $name, string $code, int $maxOccupancy): RoomType
    {
        $roomType = new RoomType([
            'name' => $name,
            'code' => $code,
            'max_occupancy' => $maxOccupancy,
        ]);
        $this->roomTypes->push($roomType);

        return $roomType;
    }

    private function inventory(Hotel $hotel, RoomType $roomType, int $quantity, float $price): void
    {
        $relation = new HotelRoomType([
            'quantity' => $quantity,
            'price' => $price,
        ]);
        $relation->hotel()->associate($hotel);
        $relation->roomType()->associate($roomType);

        $this->hotelRoomTypes->push($relation);
    }

    private function booking(
        string $locator,
        Hotel $hotel,
        RoomType $roomType,
        int $paxes,
        string $checkin,
        string $checkout,
        string $status,
    ): void {
        $booking = new Booking([
            'locator' => $locator,
            'paxes' => $paxes,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'status' => $status,
        ]);
        $booking->hotel()->associate($hotel);
        $booking->roomType()->associate($roomType);

        $this->bookings->push($booking);
    }
}
