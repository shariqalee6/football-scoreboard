<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Service\ScoreboardInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:scoreboard:demo',
    description: 'Run the assignment sample scenario and print the scoreboard summary.',
)]
final class ScoreboardDemoCommand extends Command
{
    public function __construct(private readonly ScoreboardInterface $scoreboard)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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

        $output->writeln('Football World Cup Score Board Summary:');

        foreach ($this->scoreboard->getSummary() as $index => $game) {
            $lineNumber = $index + 1;
            $output->writeln(sprintf(
                '%d. %s %d - %s %d',
                $lineNumber,
                $game->getHomeTeam(),
                $game->getHomeScore(),
                $game->getAwayTeam(),
                $game->getAwayScore(),
            ));
        }

        return Command::SUCCESS;
    }
}
