<?php

namespace Yokai\Versioning\Tests\Unit\Bridge\Symfony\Console\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Tester\CommandTester;
use Yokai\Versioning\Bridge\Symfony\Console\Command\InitializeCommand;
use Yokai\Versioning\Initialize\Initializer;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Tests\TypesConfigFactory;
use Yokai\Versioning\TypesConfig;

class InitializeCommandTest extends TestCase
{
    /**
     * @var TypesConfig
     */
    private static $typesConfig;

    /**
     * @var Initializer|ObjectProphecy
     */
    private $initializer;

    public static function setUpBeforeClass(): void
    {
        self::$typesConfig = TypesConfigFactory::get();
    }

    public static function tearDownAfterClass(): void
    {
        self::$typesConfig = null;
    }

    protected function setUp(): void
    {
        $this->initializer = $this->prophesize(Initializer::class);
    }

    protected function tearDown(): void
    {
        $this->initializer = null;
    }

    /**
     * @test
     */
    public function it_is_a_command(): void
    {
        $command = new InitializeCommand($this->initializer->reveal(), self::$typesConfig);
        self::assertNotEmpty($command->getName());
        self::assertStringStartsWith('yokai:versioning:', $command->getName());
        self::assertNotEmpty($command->getDescription());
        self::assertCount(1, $command->getDefinition()->getArguments());
        self::assertTrue($command->getDefinition()->hasArgument('type'));
        self::assertCount(1, $command->getDefinition()->getOptions());
        self::assertTrue($command->getDefinition()->hasOption('all'));
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Console\Exception\RuntimeException
     */
    public function it_expect_argument_or_option(): void
    {
        $this->initializer->initialize(Argument::any())->shouldNotBeCalled();

        $tester = new CommandTester(new InitializeCommand($this->initializer->reveal(), TypesConfigFactory::get()));

        $tester->execute([]);
    }

    /**
     * @test
     */
    public function it_call_initializer_with_single_type(): void
    {
        $this->initializer->initialize(Product::class)->shouldBeCalledTimes(1);

        $tester = new CommandTester(new InitializeCommand($this->initializer->reveal(), TypesConfigFactory::get()));

        self::assertSame(0, $tester->execute(['type' => Product::VERSION_TYPE]));
    }

    /**
     * @test
     */
    public function it_call_initializer_with_all_types(): void
    {
        $this->initializer->initialize(Product::class)->shouldBeCalledTimes(1);
        $this->initializer->initialize(Order::class)->shouldBeCalledTimes(1);
        $this->initializer->initialize(OrderItem::class)->shouldBeCalledTimes(1);

        $tester = new CommandTester(new InitializeCommand($this->initializer->reveal(), TypesConfigFactory::get()));

        self::assertSame(0, $tester->execute(['--all' => true]));
    }
}
