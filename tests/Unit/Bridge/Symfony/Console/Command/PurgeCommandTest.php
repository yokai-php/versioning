<?php

namespace Yokai\Versioning\Tests\Unit\Bridge\Symfony\Console\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Yokai\Versioning\Bridge\Symfony\Console\Command\PurgeCommand;
use Yokai\Versioning\Purge\PurgerInterface;

class PurgeCommandTest extends TestCase
{
    /**
     * @var PurgerInterface|ObjectProphecy
     */
    private $purger;

    protected function setUp(): void
    {
        $this->purger = $this->prophesize(PurgerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->purger = null;
    }

    /**
     * @test
     */
    public function it_is_a_command(): void
    {
        $command = new PurgeCommand($this->purger->reveal());
        self::assertNotEmpty($command->getName());
        self::assertStringStartsWith('yokai:versioning:', $command->getName());
        self::assertNotEmpty($command->getDescription());
        self::assertCount(0, $command->getDefinition()->getArguments());
        self::assertCount(0, $command->getDefinition()->getOptions());
    }

    /**
     * @test
     * @dataProvider verbosity
     */
    public function it_call_purger(int $verbosity, bool $display): void
    {
        $this->purger->purge()->shouldBeCalledTimes(1)->willReturn(10);

        $tester = new CommandTester(new PurgeCommand($this->purger->reveal()));

        self::assertSame(0, $tester->execute([], ['verbosity' => $verbosity]));

        if ($display) {
            self::assertContains('Total purged versions : 10.', $tester->getDisplay(true));
        }
    }

    public function verbosity(): \Generator
    {
        yield [OutputInterface::VERBOSITY_QUIET, false];
        yield [OutputInterface::VERBOSITY_NORMAL, false];
        yield [OutputInterface::VERBOSITY_VERBOSE, false];
        yield [OutputInterface::VERBOSITY_VERY_VERBOSE, true];
        yield [OutputInterface::VERBOSITY_DEBUG, true];
    }
}
