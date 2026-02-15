<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\GameSummary;

interface ScoreboardInterface
{
    public function startGame(string $homeTeam, string $awayTeam): void;

    public function finishGame(string $homeTeam, string $awayTeam): void;

    public function updateScore(string $homeTeam, string $awayTeam, int $homeScore, int $awayScore): void;

    /**
     * @return list<GameSummary>
     */
    public function getSummary(): array;
}
