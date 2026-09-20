<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use App\Services\MockDataService;
use Tests\TestCase;

class MockDataServiceTest extends TestCase
{
    public function test_service_provides_all_mock_resources(): void
    {
        $service = $this->service();

        $this->assertNotEmpty($service->hotels());
        $this->assertNotEmpty($service->roomTypes());
        $this->assertNotEmpty($service->hotelRoomTypes());
        $this->assertNotEmpty($service->bookings());
    }

    public function test_mock_resources_use_the_eloquent_models(): void
    {
        $service = $this->service();

        $this->assertContainsOnlyInstancesOf(Hotel::class, $service->hotels());
        $this->assertContainsOnlyInstancesOf(RoomType::class, $service->roomTypes());
        $this->assertContainsOnlyInstancesOf(HotelRoomType::class, $service->hotelRoomTypes());
        $this->assertContainsOnlyInstancesOf(Booking::class, $service->bookings());
    }

    public function test_mock_data_is_varied_enough_for_the_endpoints(): void
    {
        $service = $this->service();

        $this->assertGreaterThanOrEqual(3, $service->hotels()->count());
        $this->assertGreaterThanOrEqual(4, $service->roomTypes()->count());
        $this->assertGreaterThanOrEqual(8, $service->hotelRoomTypes()->count());
        $this->assertGreaterThanOrEqual(6, $service->bookings()->count());

        $this->assertGreaterThanOrEqual(2, $service->bookings()->unique(fn (Booking $booking) => $booking->hotel->code)->count());
        $this->assertGreaterThanOrEqual(2, $service->bookings()->unique(fn (Booking $booking) => $booking->roomType->code)->count());
        $this->assertGreaterThanOrEqual(2, $service->bookings()->unique(fn (Booking $booking) => $booking->checkin->toDateString())->count());
    }

    public function test_hotel_room_types_have_at_least_one_unit_and_a_non_negative_price(): void
    {
        $service = $this->service();

        foreach ($service->hotelRoomTypes() as $hotelRoomType) {
            $this->assertGreaterThanOrEqual(1, $hotelRoomType->quantity);
            $this->assertGreaterThanOrEqual(0, $hotelRoomType->price);
        }
    }

    public function test_bookings_are_coherent(): void
    {
        $service = $this->service();

        foreach ($service->bookings() as $booking) {
            $this->assertTrue(
                $service->hotelRoomTypes()->contains(
                    fn (HotelRoomType $relation) => $relation->hotel->code === $booking->hotel->code
                        && $relation->roomType->code === $booking->roomType->code
                )
            );
            $this->assertLessThanOrEqual($booking->roomType->max_occupancy, $booking->paxes);
            $this->assertTrue($booking->checkin->lt($booking->checkout));
            $this->assertContains($booking->status, ['CONFIRMED', 'CANCELLED']);
        }
    }

    public function test_lookup_helpers_are_available(): void
    {
        $service = $this->service();

        $this->assertSame('GRAND', $service->hotelByCode('GRAND')?->code);
        $this->assertNull($service->hotelByCode('UNKNOWN'));
        $this->assertSame('DELUXE', $service->roomTypeByCode('DELUXE')?->code);
        $this->assertNull($service->roomTypeByCode('UNKNOWN'));
    }

    private function service(): MockDataService
    {
        return app(MockDataService::class);
    }
}
