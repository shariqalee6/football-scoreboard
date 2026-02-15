<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Domain\Model\Game;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testConstructionSetsTeamsAndInitialScore(): void
    {
        $game = new Game('Mexico', 'Canada');

        self::assertSame('Mexico', $game->getHomeTeam());
        self::assertSame('Canada', $game->getAwayTeam());
        self::assertSame(0, $game->getHomeScore());
        self::assertSame(0, $game->getAwayScore());
    }

    public function testConstructionTrimsTeamNames(): void
    {
        $game = new Game('  Mexico  ', '  Canada  ');

        self::assertSame('Mexico', $game->getHomeTeam());
        self::assertSame('Canada', $game->getAwayTeam());
    }

    public function testTotalScoreReturnsSum(): void
    {
        $game = new Game('Mexico', 'Canada');
        $game->updateScore(3, 2);

        self::assertSame(5, $game->totalScore());
    }

    public function testUpdateScoreChangesScores(): void
    {
        $game = new Game('Spain', 'Brazil');
        $game->updateScore(10, 2);

        self::assertSame(10, $game->getHomeScore());
        self::assertSame(2, $game->getAwayScore());
    }

    public function testUpdateScoreAllowsZero(): void
    {
        $game = new Game('Spain', 'Brazil');
        $game->updateScore(0, 0);

        self::assertSame(0, $game->totalScore());
    }

}
