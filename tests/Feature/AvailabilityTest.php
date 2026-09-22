<?php

namespace Tests\Feature;

use App\Contracts\DataService;
use App\Models\Booking;
use Database\Seeders\DomainDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DomainDataSeeder::class);
    }

    public function test_availability_without_overlapping_bookings(): void
    {
        $response = $this->postAvailability([
            'hotel' => 'GRAND',
            'roomType' => 'DELUXE',
            'paxes' => 2,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ]);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.hotel.code', 'GRAND')
            ->assertJsonPath('0.roomType.code', 'DELUXE');
    }

    public function test_overlapping_bookings_reduce_the_available_inventory(): void
    {
        $this->occupy('PANORAMA', 'SUITE', 2, '2026-11-01', '2026-11-05');

        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'SUITE',
            'paxes' => 2,
            'checkin' => '2026-11-01',
            'checkout' => '2026-11-05',
        ])->assertOk()->assertJsonCount(1);

        $this->occupy('PANORAMA', 'SUITE', 1, '2026-11-01', '2026-11-05');

        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'SUITE',
            'paxes' => 2,
            'checkin' => '2026-11-01',
            'checkout' => '2026-11-05',
        ])->assertOk()->assertJsonCount(0);
    }

    public function test_availability_is_empty_when_all_units_are_booked(): void
    {
        $this->occupy('GRAND', 'SUITE', 5, '2026-11-01', '2026-11-05');

        $this->postAvailability([
            'hotel' => 'GRAND',
            'roomType' => 'SUITE',
            'paxes' => 3,
            'checkin' => '2026-11-01',
            'checkout' => '2026-11-05',
        ])->assertOk()->assertJsonCount(0);
    }

    public function test_cancelled_bookings_do_not_reduce_the_available_inventory(): void
    {
        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-12-02',
            'checkout' => '2026-12-03',
        ])->assertOk()->assertJsonCount(1);
    }

    public function test_bookings_that_do_not_overlap_do_not_reduce_the_available_inventory(): void
    {
        $this->occupy('PANORAMA', 'SUITE', 3, '2026-11-01', '2026-11-05');

        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'SUITE',
            'paxes' => 2,
            'checkin' => '2026-11-10',
            'checkout' => '2026-11-12',
        ])->assertOk()->assertJsonCount(1);
    }

    public function test_availability_changes_with_the_requested_interval(): void
    {
        $this->occupy('PANORAMA', 'SUITE', 3, '2026-11-01', '2026-11-05');

        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'SUITE',
            'paxes' => 2,
            'checkin' => '2026-11-01',
            'checkout' => '2026-11-05',
        ])->assertOk()->assertJsonCount(0);

        $this->postAvailability([
            'hotel' => 'PANORAMA',
            'roomType' => 'SUITE',
            'paxes' => 2,
            'checkin' => '2026-11-10',
            'checkout' => '2026-11-12',
        ])->assertOk()->assertJsonCount(1);
    }

    public function test_availability_excludes_room_types_with_insufficient_capacity(): void
    {
        $this->postAvailability([
            'hotel' => 'GRAND',
            'paxes' => 3,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonCount(1)->assertJsonPath('0.roomType.code', 'SUITE');

        $this->postAvailability([
            'hotel' => 'GRAND',
            'paxes' => 4,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonCount(0);
    }

    public function test_availability_can_be_filtered_by_hotel(): void
    {
        $response = $this->postAvailability([
            'hotel' => 'GRAND',
            'paxes' => 2,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ]);

        $response->assertOk()->assertJsonCount(3);

        foreach ($response->json() as $availability) {
            $this->assertSame('GRAND', $availability['hotel']['code']);
        }
    }

    public function test_availability_can_be_filtered_by_room_type(): void
    {
        $response = $this->postAvailability([
            'roomType' => 'DELUXE',
            'paxes' => 2,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ]);

        $response->assertOk()->assertJsonCount(2);

        foreach ($response->json() as $availability) {
            $this->assertSame('DELUXE', $availability['roomType']['code']);
        }
    }

    public function test_availability_can_be_filtered_by_hotel_and_room_type(): void
    {
        $this->postAvailability([
            'hotel' => 'GRAND',
            'roomType' => 'DELUXE',
            'paxes' => 2,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.hotel.code', 'GRAND')
            ->assertJsonPath('0.roomType.code', 'DELUXE');
    }

    public function test_availability_without_filters_returns_all_matching_room_types(): void
    {
        $this->postAvailability([
            'paxes' => 2,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonCount(8);
    }

    public function test_availability_returns_the_price_of_the_hotel_room_type(): void
    {
        $this->postAvailability([
            'hotel' => 'GRAND',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonPath('0.price', 85.5);

        $this->postAvailability([
            'hotel' => 'DELFI',
            'roomType' => 'FAMILY',
            'paxes' => 4,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonPath('0.price', 175);
    }

    public function test_a_created_booking_reduces_the_available_inventory(): void
    {
        $this->postJson('/api/v1/bookings', [
            'hotel' => 'GRAND',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertCreated();

        $this->postAvailability([
            'hotel' => 'GRAND',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-10-10',
            'checkout' => '2026-10-12',
        ])->assertOk()->assertJsonCount(1);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postAvailability(array $payload): TestResponse
    {
        return $this->postJson('/api/v1/availability', $payload);
    }

    private function occupy(string $hotelCode, string $roomTypeCode, int $units, string $checkin, string $checkout): void
    {
        $service = app(DataService::class);
        $hotel = $service->hotelByCode($hotelCode);
        $roomType = $service->roomTypeByCode($roomTypeCode);

        for ($unit = 0; $unit < $units; $unit++) {
            $booking = new Booking([
                'locator' => Str::upper(Str::random(6)),
                'paxes' => 1,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'status' => 'CONFIRMED',
            ]);
            $booking->hotel()->associate($hotel);
            $booking->roomType()->associate($roomType);

            $service->addBooking($booking);
        }
    }
}
