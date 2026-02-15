<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use InvalidArgumentException;

final class InvalidTeamException extends InvalidArgumentException
{
    public static function emptyName(): self
    {
        return new self('Team name cannot be empty.');
    }

    public static function sameTeams(string $team): self
    {
        return new self(sprintf('Home and away teams cannot be the same: "%s".', $team));
    }
}
