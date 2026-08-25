<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\BookingConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_notifications_list_returns_notifications(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Seed a fake notification
        $user->notifications()->create([
            'id'              => Str::uuid(),
            'type'            => 'App\Notifications\BookingConfirmed',
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['type' => 'booking_confirmed', 'message' => 'Test', 'booking_id' => 1, 'booking_code' => 'BK-TEST', 'url' => '/bookings/1']),
        ]);

        $this->withToken($token)
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }

    public function test_api_unread_count_returns_number(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonStructure(['count']);
    }

    public function test_api_mark_read_marks_notification(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $id = Str::uuid()->toString();
        $user->notifications()->create([
            'id'              => $id,
            'type'            => 'App\Notifications\BookingConfirmed',
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['type' => 'booking_confirmed', 'message' => 'Test', 'booking_id' => 1, 'booking_code' => 'BK-TEST', 'url' => '/bookings/1']),
        ]);

        $this->withToken($token)
            ->postJson("/api/notifications/{$id}/read")
            ->assertOk();

        $this->assertNotNull($user->notifications()->find($id)->read_at);
    }

    public function test_api_read_all_marks_all_read(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Create 3 unread notifications
        for ($i = 0; $i < 3; $i++) {
            $user->notifications()->create([
                'id'              => Str::uuid(),
                'type'            => 'App\Notifications\BookingConfirmed',
                'notifiable_type' => User::class,
                'notifiable_id'   => $user->id,
                'data'            => json_encode(['type' => 'booking_confirmed', 'message' => "Test {$i}", 'booking_id' => $i, 'booking_code' => "BK-{$i}", 'url' => "/bookings/{$i}"]),
            ]);
        }

        $this->withToken($token)
            ->postJson('/api/notifications/read-all')
            ->assertOk();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
