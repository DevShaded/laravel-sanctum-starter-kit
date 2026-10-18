<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class EnsureApplicationKeyTest extends TestCase
{
    private string $environmentFile;

    protected function setUp(): void
    {
        parent::setUp();

        $environmentFile = tempnam(sys_get_temp_dir(), 'starter-kit-env-');
        self::assertIsString($environmentFile);
        $this->environmentFile = $environmentFile;
        $this->app->useEnvironmentPath(dirname($environmentFile));
        $this->app->loadEnvironmentFrom(basename($environmentFile));
    }

    protected function tearDown(): void
    {
        unlink($this->environmentFile);

        parent::tearDown();
    }

    public function test_generates_a_missing_key_and_preserves_other_environment_values(): void
    {
        file_put_contents($this->environmentFile, "APP_KEY=\nAPP_NAME=Starter\n");
        config(['app.key' => '']);

        $command = $this->artisan('app:ensure-key');
        self::assertInstanceOf(PendingCommand::class, $command);
        $command->assertSuccessful()->run();

        self::assertMatchesRegularExpression('/^APP_KEY=base64:[A-Za-z0-9+\/]{43}=\nAPP_NAME=Starter\n$/', (string) file_get_contents($this->environmentFile));
    }

    public function test_preserves_an_existing_key_on_repeated_runs(): void
    {
        $contents = 'APP_KEY=base64:'.base64_encode(str_repeat('a', 32))."\n";
        file_put_contents($this->environmentFile, $contents);
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);

        $command = $this->artisan('app:ensure-key');
        self::assertInstanceOf(PendingCommand::class, $command);
        $command->assertSuccessful()->run();
        $command = $this->artisan('app:ensure-key');
        self::assertInstanceOf(PendingCommand::class, $command);
        $command->assertSuccessful()->run();

        self::assertSame($contents, file_get_contents($this->environmentFile));
    }

    public function test_fails_if_the_environment_file_has_no_key_placeholder(): void
    {
        file_put_contents($this->environmentFile, "APP_NAME=Starter\n");
        config(['app.key' => '']);

        $command = $this->artisan('app:ensure-key');
        self::assertInstanceOf(PendingCommand::class, $command);
        $command->assertFailed()->run();

        self::assertSame("APP_NAME=Starter\n", file_get_contents($this->environmentFile));
    }
}
