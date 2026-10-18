<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class TokenPruningTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_daily_pruning_removes_old_expired_tokens_and_keeps_other_tokens(): void
    {
        $this->freezeTime();
        $expiredToken = PersonalAccessToken::factory()->create(['expires_at' => now()->subHours(25)]);
        $recentlyExpiredToken = PersonalAccessToken::factory()->create(['expires_at' => now()->subHour()]);
        $activeToken = PersonalAccessToken::factory()->create(['expires_at' => now()->addDay()]);
        $nonExpiringToken = PersonalAccessToken::factory()->create();
        $events = array_values(array_filter(
            resolve(Schedule::class)->events(),
            fn (Event $event): bool => str_contains($event->command ?? '', 'sanctum:prune-expired'),
        ));
        self::assertCount(1, $events);
        self::assertSame('0 0 * * *', $events[0]->expression);
        self::assertIsString($events[0]->command);
        self::assertStringContainsString('sanctum:prune-expired --hours=24', $events[0]->command);

        $command = $this->artisan('sanctum:prune-expired', ['--hours' => 24]);
        self::assertInstanceOf(PendingCommand::class, $command);
        $command->assertSuccessful()->run();

        $this->assertModelMissing($expiredToken);
        $this->assertModelExists($recentlyExpiredToken);
        $this->assertModelExists($activeToken);
        $this->assertModelExists($nonExpiringToken);
    }
}
