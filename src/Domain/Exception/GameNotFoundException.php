<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;

final class GameNotFoundException extends RuntimeException
{
    public static function create(string $homeTeam, string $awayTeam): self
    {
        return new self(sprintf(
            'Game "%s" vs "%s" was not found on the scoreboard.',
            $homeTeam,
            $awayTeam,
        ));
    }
}
