<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

#[Signature('app:ensure-key')]
#[Description('Generate an application key only when one is missing')]
final class EnsureApplicationKey extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->hasApplicationKey()) {
            $this->components->info('Application key already exists.');

            return self::SUCCESS;
        }

        $this->call('key:generate');

        return $this->hasApplicationKey() ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Read mutable configuration that the nested key:generate command updates.
     *
     * @phpstan-impure
     */
    private function hasApplicationKey(): bool
    {
        return filled(Config::get('app.key'));
    }
}
