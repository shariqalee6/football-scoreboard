<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidTeamException;

final class TeamName
{
    public static function normalize(string $teamName): string
    {
        $trimmed = trim($teamName);

        if ($trimmed === '') {
            throw InvalidTeamException::emptyName();
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($trimmed, 'UTF-8');
        }

        return strtolower($trimmed);
    }
}
