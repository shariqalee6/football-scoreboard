<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Domain\Exception\GameAlreadyExistsException;
use App\Domain\Exception\GameNotFoundException;
use App\Domain\Exception\InvalidScoreException;
use App\Domain\Exception\InvalidTeamException;
use App\Domain\Model\GameSummary;
use App\Domain\Service\Scoreboard;
use PHPUnit\Framework\TestCase;

final class ScoreboardTest extends TestCase
{
    private Scoreboard $scoreboard;

    protected function setUp(): void
    {
        $this->scoreboard = new Scoreboard();
    }

    public function testStartGameCreatesMatchWithZeroScore(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $summary = $this->scoreboard->getSummary();
        $game = $summary[0];

        self::assertSame('Mexico', $game->getHomeTeam());
        self::assertSame('Canada', $game->getAwayTeam());
        self::assertSame(0, $game->getHomeScore());
        self::assertSame(0, $game->getAwayScore());
    }

    public function testStartGameAppearsInSummary(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');

        self::assertCount(1, $this->scoreboard->getSummary());
    }

    public function testStartDuplicateGameThrowsException(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');

        $this->expectException(GameAlreadyExistsException::class);
        $this->scoreboard->startGame('Mexico', 'Canada');
    }

    public function testStartDuplicateGameIsCaseInsensitiveAndTrimmed(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');

        $this->expectException(GameAlreadyExistsException::class);
        $this->scoreboard->startGame(' mexico ', ' CANADA ');
    }

    public function testReversedHomeAndAwayTeamsAreDifferentGames(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->startGame('Canada', 'Mexico');

        self::assertCount(2, $this->scoreboard->getSummary());
    }

    public function testStartGameWithInvalidTeamThrowsException(): void
    {
        $this->expectException(InvalidTeamException::class);
        $this->scoreboard->startGame('', 'Canada');
    }

    public function testFinishGameRemovesItFromSummary(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->finishGame('Mexico', 'Canada');

        self::assertCount(0, $this->scoreboard->getSummary());
    }

    public function testFinishNonExistentGameThrowsException(): void
    {
        $this->expectException(GameNotFoundException::class);
        $this->scoreboard->finishGame('Mexico', 'Canada');
    }

    public function testFinishLookupIsCaseInsensitiveAndTrimmed(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->finishGame(' mexico ', ' CANADA ');

        self::assertSame([], $this->scoreboard->getSummary());
    }

    public function testUpdateScoreChangesGameScore(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->updateScore('Mexico', 'Canada', 0, 5);

        $summary = $this->scoreboard->getSummary();
        self::assertSame(0, $summary[0]->getHomeScore());
        self::assertSame(5, $summary[0]->getAwayScore());
    }

    public function testSummaryReturnsGameSummaryWithCorrectTotal(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->updateScore('Mexico', 'Canada', 1, 2);
        $summary = $this->scoreboard->getSummary();

        self::assertInstanceOf(GameSummary::class, $summary[0]);
        self::assertSame(3, $summary[0]->getTotalScore());
    }

    public function testUpdateScoreForNonExistentGameThrowsException(): void
    {
        $this->expectException(GameNotFoundException::class);
        $this->scoreboard->updateScore('Mexico', 'Canada', 1, 0);
    }

    public function testUpdateScoreRejectsNegativeValues(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');

        $this->expectException(InvalidScoreException::class);
        $this->scoreboard->updateScore('Mexico', 'Canada', -1, 0);
    }

    public function testSummaryIsEmptyWhenNoGamesExist(): void
    {
        self::assertSame([], $this->scoreboard->getSummary());
    }

    public function testSummaryOrderedByTotalScoreDescending(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->updateScore('Mexico', 'Canada', 0, 1);

        $this->scoreboard->startGame('Spain', 'Brazil');
        $this->scoreboard->updateScore('Spain', 'Brazil', 10, 2);

        $summary = $this->scoreboard->getSummary();

        self::assertSame('Spain', $summary[0]->getHomeTeam());
        self::assertSame('Mexico', $summary[1]->getHomeTeam());
    }

    public function testSummaryTieBrokenByMostRecentlyAdded(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->updateScore('Mexico', 'Canada', 1, 1);

        $this->scoreboard->startGame('Spain', 'Brazil');
        $this->scoreboard->updateScore('Spain', 'Brazil', 1, 1);

        $summary = $this->scoreboard->getSummary();

        self::assertSame('Spain', $summary[0]->getHomeTeam());
        self::assertSame('Mexico', $summary[1]->getHomeTeam());
    }

    public function testFullScenarioFromTaskDescription(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->updateScore('Mexico', 'Canada', 0, 5);

        $this->scoreboard->startGame('Spain', 'Brazil');
        $this->scoreboard->updateScore('Spain', 'Brazil', 10, 2);

        $this->scoreboard->startGame('Germany', 'France');
        $this->scoreboard->updateScore('Germany', 'France', 2, 2);

        $this->scoreboard->startGame('Uruguay', 'Italy');
        $this->scoreboard->updateScore('Uruguay', 'Italy', 6, 6);

        $this->scoreboard->startGame('Argentina', 'Australia');
        $this->scoreboard->updateScore('Argentina', 'Australia', 3, 1);

        $summary = $this->scoreboard->getSummary();

        self::assertCount(5, $summary);
        self::assertSame('Uruguay', $summary[0]->getHomeTeam());
        self::assertSame('Spain', $summary[1]->getHomeTeam());
        self::assertSame('Mexico', $summary[2]->getHomeTeam());
        self::assertSame('Argentina', $summary[3]->getHomeTeam());
        self::assertSame('Germany', $summary[4]->getHomeTeam());
    }

    public function testCanStartNewGameAfterFinishingIt(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->finishGame('Mexico', 'Canada');
        $this->scoreboard->startGame('Mexico', 'Canada');
        $summary = $this->scoreboard->getSummary();
        $game = $summary[0];

        self::assertSame(0, $game->getHomeScore());
        self::assertCount(1, $this->scoreboard->getSummary());
    }

    public function testMultipleGamesCanBeManagedIndependently(): void
    {
        $this->scoreboard->startGame('Mexico', 'Canada');
        $this->scoreboard->startGame('Spain', 'Brazil');

        $this->scoreboard->updateScore('Mexico', 'Canada', 2, 1);
        $this->scoreboard->finishGame('Spain', 'Brazil');

        $summary = $this->scoreboard->getSummary();
        self::assertCount(1, $summary);
        self::assertSame('Mexico', $summary[0]->getHomeTeam());
        self::assertSame(2, $summary[0]->getHomeScore());
    }
}
