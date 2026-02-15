<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\ScoreboardDemoCommand;
use App\Domain\Service\Scoreboard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ScoreboardDemoCommandTest extends TestCase
{
    public function testDemoCommandPrintsExpectedSummaryOrder(): void
    {
        $command = new ScoreboardDemoCommand(new Scoreboard());
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([]);
        $display = $tester->getDisplay();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Football World Cup Score Board Summary:', $display);
        self::assertStringContainsString('1. Uruguay 6 - Italy 6', $display);
        self::assertStringContainsString('2. Spain 10 - Brazil 2', $display);
        self::assertStringContainsString('3. Mexico 0 - Canada 5', $display);
        self::assertStringContainsString('4. Argentina 3 - Australia 1', $display);
        self::assertStringContainsString('5. Germany 2 - France 2', $display);
    }
}
