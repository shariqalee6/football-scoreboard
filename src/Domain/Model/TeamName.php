<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidTeamException;

final class TeamName
{
    /**
     * Returns a trimmed, non-empty name for display (e.g. in summaries).
     * Rejects empty or whitespace-only names.
     */
    public static function normalizeForDisplay(string $teamName): string
    {
        $trimmed = trim($teamName);

        if ($trimmed === '') {
            throw InvalidTeamException::emptyName();
        }

        return $trimmed;
    }

    /**
     * Returns a normalized key form (trimmed, lowercased) for identity and lookups.
     * Uses mb_strtolower when available for UTF-8 safety; fallback for minimal PHP installs without mbstring.
     */
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
