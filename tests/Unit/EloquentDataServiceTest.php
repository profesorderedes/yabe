<?php

namespace Tests\Unit;

use App\Contracts\DataService;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\RoomType;
use Database\Seeders\DomainDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentDataServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DomainDataSeeder::class);
    }

    public function test_service_provides_all_persisted_resources(): void
    {
        $service = $this->service();

        $this->assertNotEmpty($service->hotels());
        $this->assertNotEmpty($service->roomTypes());
        $this->assertNotEmpty($service->hotelRoomTypes());
        $this->assertNotEmpty($service->bookings());
    }

    public function test_resources_use_the_eloquent_models(): void
    {
        $service = $this->service();

        $this->assertContainsOnlyInstancesOf(Hotel::class, $service->hotels());
        $this->assertContainsOnlyInstancesOf(RoomType::class, $service->roomTypes());
        $this->assertContainsOnlyInstancesOf(HotelRoomType::class, $service->hotelRoomTypes());
        $this->assertContainsOnlyInstancesOf(Booking::class, $service->bookings());
    }

    public function test_seeded_data_is_varied_enough_for_the_endpoints(): void
    {
        $service = $this->service();

        $this->assertGreaterThanOrEqual(3, $service->hotels()->count());
        $this->assertGreaterThanOrEqual(4, $service->roomTypes()->count());
        $this->assertGreaterThanOrEqual(8, $service->hotelRoomTypes()->count());
        $this->assertGreaterThanOrEqual(6, $service->bookings()->count());
    }

    public function test_hotel_room_types_have_at_least_one_unit_and_a_non_negative_price(): void
    {
        $service = $this->service();

        foreach ($service->hotelRoomTypes() as $hotelRoomType) {
            $this->assertGreaterThanOrEqual(1, $hotelRoomType->quantity);
            $this->assertGreaterThanOrEqual(0, $hotelRoomType->price);
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

    public function test_add_booking_persists_the_booking(): void
    {
        $service = $this->service();

        $booking = new Booking([
            'locator' => Str::upper(Str::random(6)),
            'paxes' => 2,
            'checkin' => '2026-12-01',
            'checkout' => '2026-12-05',
            'status' => 'CONFIRMED',
        ]);
        $booking->hotel()->associate($service->hotelByCode('GRAND'));
        $booking->roomType()->associate($service->roomTypeByCode('DELUXE'));

        $service->addBooking($booking);

        $this->assertDatabaseHas('bookings', [
            'locator' => $booking->locator,
            'status' => 'CONFIRMED',
        ]);
        $this->assertTrue($service->bookings()->contains(fn (Booking $persisted) => $persisted->locator === $booking->locator));
    }

    private function service(): DataService
    {
        return app(DataService::class);
    }
}
