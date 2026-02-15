<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use InvalidArgumentException;

final class InvalidScoreException extends InvalidArgumentException
{
    public static function negativeScore(int $score): self
    {
        return new self(sprintf('Score cannot be negative, got: %d.', $score));
    }
}
