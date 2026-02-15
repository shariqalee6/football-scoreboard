<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Domain\Model\GameSummary;
use PHPUnit\Framework\TestCase;

final class GameSummaryTest extends TestCase
{
    public function testGettersAndTotalScore(): void
    {
        $summary = new GameSummary('Mexico', 'Canada', 3, 2);

        self::assertSame('Mexico', $summary->getHomeTeam());
        self::assertSame('Canada', $summary->getAwayTeam());
        self::assertSame(3, $summary->getHomeScore());
        self::assertSame(2, $summary->getAwayScore());
        self::assertSame(5, $summary->getTotalScore());
    }
}
