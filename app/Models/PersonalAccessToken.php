<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PersonalAccessTokenFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Override;

final class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /** @use HasFactory<PersonalAccessTokenFactory> */
    use HasFactory;

    use HasUuids;

    #[Override]
    public static function findToken(mixed $token): ?static
    {
        if (str_contains($token, '|') && ! Str::isUuid(explode('|', $token, 2)[0])) {
            return null;
        }

        return parent::findToken($token);
    }
}
