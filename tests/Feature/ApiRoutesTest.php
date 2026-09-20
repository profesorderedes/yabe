<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    #[DataProvider('routeProvider')]
    public function test_api_routes_are_reachable(string $method, string $uri, int $status, array $body): void
    {
        $response = $this->{$method}($uri, $body);

        $response->assertStatus($status);
    }

    public static function routeProvider(): array
    {
        return [
            'list hotels' => ['getJson', '/api/v1/hotels', 200, []],
            'list room types' => ['getJson', '/api/v1/room-types', 200, []],
            'check availability' => ['postJson', '/api/v1/availability', 200, [
                'hotel' => 'GRAND',
                'roomType' => 'DELUXE',
                'paxes' => 2,
                'checkin' => '2026-10-06',
                'checkout' => '2026-10-09',
            ]],
            'create booking' => ['postJson', '/api/v1/bookings', 201, [
                'hotel' => 'GRAND',
                'roomType' => 'STANDARD',
                'paxes' => 1,
                'checkin' => '2026-12-15',
                'checkout' => '2026-12-20',
            ]],
        ];
    }
}
