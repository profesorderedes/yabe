<?php

namespace Tests\Feature;

use Database\Seeders\DomainDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DomainDataSeeder::class);
    }

    public function test_hotels_endpoint_returns_hotels_with_room_types(): void
    {
        $response = $this->getJson('/api/v1/hotels');

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'code',
                    'roomTypes' => [
                        '*' => [
                            'roomType' => ['name', 'code', 'maxOccupancy'],
                            'quantity',
                            'price',
                        ],
                    ],
                ],
            ]);
    }

    public function test_room_types_endpoint_returns_room_types(): void
    {
        $response = $this->getJson('/api/v1/room-types');

        $response->assertOk()
            ->assertJsonCount(4)
            ->assertJsonStructure([
                '*' => ['name', 'code', 'maxOccupancy'],
            ]);
    }

    public function test_availability_endpoint_returns_available_rooms(): void
    {
        $response = $this->postJson('/api/v1/availability', [
            'hotel' => 'GRAND',
            'roomType' => 'DELUXE',
            'paxes' => 2,
            'checkin' => '2026-10-06',
            'checkout' => '2026-10-09',
        ]);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'hotel' => ['code' => 'GRAND'],
                    'roomType' => ['code' => 'DELUXE'],
                    'price' => 125.5,
                ],
            ]);
    }

    public function test_availability_endpoint_filters_by_guest_capacity(): void
    {
        $response = $this->postJson('/api/v1/availability', [
            'hotel' => 'GRAND',
            'paxes' => 4,
            'checkin' => '2026-12-01',
            'checkout' => '2026-12-03',
        ]);

        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function test_availability_endpoint_ignores_cancelled_bookings(): void
    {
        $response = $this->postJson('/api/v1/availability', [
            'hotel' => 'PANORAMA',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-12-02',
            'checkout' => '2026-12-03',
        ]);

        $response->assertOk()
            ->assertJsonCount(1);
    }

    public function test_bookings_endpoint_creates_a_booking(): void
    {
        $response = $this->postJson('/api/v1/bookings', [
            'hotel' => 'GRAND',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-12-15',
            'checkout' => '2026-12-20',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'CONFIRMED');
    }

    public function test_bookings_endpoint_rejects_an_unknown_hotel(): void
    {
        $response = $this->postJson('/api/v1/bookings', [
            'hotel' => 'UNKNOWN',
            'roomType' => 'STANDARD',
            'paxes' => 1,
            'checkin' => '2026-12-15',
            'checkout' => '2026-12-20',
        ]);

        $response->assertStatus(422);
    }
}
