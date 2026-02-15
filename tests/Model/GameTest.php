<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Domain\Exception\InvalidScoreException;
use App\Domain\Exception\InvalidTeamException;
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

    public function testUpdateScoreThrowsOnNegativeHomeScore(): void
    {
        $this->expectException(InvalidScoreException::class);

        $game = new Game('Germany', 'France');
        $game->updateScore(-1, 0);
    }

    public function testUpdateScoreThrowsOnNegativeAwayScore(): void
    {
        $this->expectException(InvalidScoreException::class);

        $game = new Game('Germany', 'France');
        $game->updateScore(0, -1);
    }

    public function testConstructorThrowsOnEmptyHomeTeam(): void
    {
        $this->expectException(InvalidTeamException::class);

        new Game('', 'Brazil');
    }

    public function testConstructorThrowsOnEmptyAwayTeam(): void
    {
        $this->expectException(InvalidTeamException::class);

        new Game('Brazil', '');
    }

    public function testConstructorThrowsOnWhitespaceOnlyTeam(): void
    {
        $this->expectException(InvalidTeamException::class);

        new Game('   ', 'Brazil');
    }

    public function testConstructorThrowsOnSameTeams(): void
    {
        $this->expectException(InvalidTeamException::class);

        new Game('Brazil', 'Brazil');
    }

    public function testConstructorThrowsOnSameTeamsCaseInsensitive(): void
    {
        $this->expectException(InvalidTeamException::class);

        new Game('brazil', 'BRAZIL');
    }
}
