<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidScoreException;
use App\Domain\Exception\InvalidTeamException;
use App\Domain\Model\TeamName;

final class Game
{
    private string $homeTeam;
    private string $awayTeam;
    private int $homeScore = 0;
    private int $awayScore = 0;

    public function __construct(string $homeTeam, string $awayTeam)
    {
        $this->validateTeams($homeTeam, $awayTeam);
        $this->homeTeam = trim($homeTeam);
        $this->awayTeam = trim($awayTeam);
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

    public function totalScore(): int
    {
        return $this->homeScore + $this->awayScore;
    }

    public function updateScore(int $homeScore, int $awayScore): void
    {
        if ($homeScore < 0) {
            throw InvalidScoreException::negativeScore($homeScore);
        }

        if ($awayScore < 0) {
            throw InvalidScoreException::negativeScore($awayScore);
        }

        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    private function validateTeams(string $homeTeam, string $awayTeam): void
    {
        if (trim($homeTeam) === '') {
            throw InvalidTeamException::emptyName();
        }

        if (trim($awayTeam) === '') {
            throw InvalidTeamException::emptyName();
        }

        $normalizedHome = TeamName::normalize($homeTeam);
        $normalizedAway = TeamName::normalize($awayTeam);

        if ($normalizedHome === $normalizedAway) {
            throw InvalidTeamException::sameTeams(trim($homeTeam));
        }
    }

    
}
