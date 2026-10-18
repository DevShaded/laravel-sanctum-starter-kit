<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PersonalAccessToken>
 */
final class PersonalAccessTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tokenable_type' => (new User)->getMorphClass(),
            'tokenable_id' => User::factory(),
            'name' => 'test-token',
            'token' => hash('sha256', Str::random(40)),
            'abilities' => ['*'],
        ];
    }
}
