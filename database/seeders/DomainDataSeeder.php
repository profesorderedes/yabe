<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class DomainDataSeeder extends Seeder
{
    /**
     * Seed the application's domain data.
     */
    public function run(): void
    {
        $grand = Hotel::firstOrCreate(['code' => 'GRAND'], ['name' => 'Grand Hotel']);
        $panorama = Hotel::firstOrCreate(['code' => 'PANORAMA'], ['name' => 'Panorama View']);
        $delfi = Hotel::firstOrCreate(['code' => 'DELFI'], ['name' => 'Delfi Suites']);

        $standard = RoomType::firstOrCreate(['code' => 'STANDARD'], ['name' => 'Standard Room', 'max_occupancy' => 2]);
        $deluxe = RoomType::firstOrCreate(['code' => 'DELUXE'], ['name' => 'Deluxe Room', 'max_occupancy' => 2]);
        $suite = RoomType::firstOrCreate(['code' => 'SUITE'], ['name' => 'Suite', 'max_occupancy' => 3]);
        $family = RoomType::firstOrCreate(['code' => 'FAMILY'], ['name' => 'Family Room', 'max_occupancy' => 4]);

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

    private function inventory(Hotel $hotel, RoomType $roomType, int $quantity, float $price): void
    {
        HotelRoomType::firstOrCreate(
            ['hotel_id' => $hotel->id, 'room_type_id' => $roomType->id],
            ['quantity' => $quantity, 'price' => $price],
        );
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
        Booking::firstOrCreate(
            ['locator' => $locator],
            [
                'hotel_id' => $hotel->id,
                'room_type_id' => $roomType->id,
                'paxes' => $paxes,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'status' => $status,
            ],
        );
    }
}
