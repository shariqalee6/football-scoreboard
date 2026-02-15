<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class GameSummary
{
    public function __construct(
        private string $homeTeam,
        private string $awayTeam,
        private int $homeScore,
        private int $awayScore,
    ) {
    }

    public function getHomeTeam(): string
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): string
    {
        return $this->awayTeam;
    }

    public function getHomeScore(): int
    {
        return $this->homeScore;
    }

    public function getAwayScore(): int
    {
        return $this->awayScore;
    }

    public function getTotalScore(): int
    {
        return $this->homeScore + $this->awayScore;
    }
}
