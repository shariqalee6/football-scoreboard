<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Exception\GameAlreadyExistsException;
use App\Domain\Exception\GameNotFoundException;
use App\Domain\Model\Game;
use App\Domain\Model\GameSummary;
use App\Domain\Model\TeamName;

final class Scoreboard implements ScoreboardInterface
{
    /** Character used only for indexing; team names cannot contain NUL in PHP. */
    private const KEY_SEPARATOR = "\0";

    /** @var array<string, Game> */
    private array $games = [];

    /** @var array<string, int> */
    private array $gameStartOrder = [];

    private int $nextStartOrder = 1;

    public function startGame(string $homeTeam, string $awayTeam): void
    {
        $key = $this->buildKey($homeTeam, $awayTeam);

        if (isset($this->games[$key])) {
            throw GameAlreadyExistsException::create($homeTeam, $awayTeam);
        }

        $game = new Game($homeTeam, $awayTeam);
        $this->games[$key] = $game;
        $this->gameStartOrder[$key] = $this->nextStartOrder++;
    }

    public function finishGame(string $homeTeam, string $awayTeam): void
    {
        $key = $this->buildKey($homeTeam, $awayTeam);
        $this->requireExistingGame($key, $homeTeam, $awayTeam);

        unset($this->games[$key], $this->gameStartOrder[$key]);
    }

    public function updateScore(string $homeTeam, string $awayTeam, int $homeScore, int $awayScore): void
    {
        $key = $this->buildKey($homeTeam, $awayTeam);
        $this->requireExistingGame($key, $homeTeam, $awayTeam);

        $this->games[$key]->updateScore($homeScore, $awayScore);
    }

    public function getSummary(): array
    {
        $games = array_values($this->games);

        usort($games, function (Game $left, Game $right): int {
            $scoreOrder = $right->totalScore() <=> $left->totalScore();

            if ($scoreOrder !== 0) {
                return $scoreOrder;
            }

            $leftOrder = $this->gameStartOrder[$this->buildKey($left->getHomeTeam(), $left->getAwayTeam())];
            $rightOrder = $this->gameStartOrder[$this->buildKey($right->getHomeTeam(), $right->getAwayTeam())];

            return $rightOrder <=> $leftOrder;
        });

        return array_map(
            static fn (Game $game): GameSummary => new GameSummary(
                $game->getHomeTeam(),
                $game->getAwayTeam(),
                $game->getHomeScore(),
                $game->getAwayScore(),
            ),
            $games,
        );
    }

    private function requireExistingGame(string $key, string $homeTeam, string $awayTeam): void
    {
        if (!isset($this->games[$key])) {
            throw GameNotFoundException::create($homeTeam, $awayTeam);
        }
    }

    /** Builds an internal lookup key from normalized home/away names. */
    private function buildKey(string $homeTeam, string $awayTeam): string
    {
        return TeamName::normalize($homeTeam) . self::KEY_SEPARATOR . TeamName::normalize($awayTeam);
    }
}
