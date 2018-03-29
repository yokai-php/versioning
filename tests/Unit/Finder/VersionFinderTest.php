<?php

namespace Yokai\Versioning\Tests\Unit\Finder;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Finder\VersionFinder;
use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Tests\Acme\Domain\User;
use Yokai\Versioning\Tests\TypesConfigFactory;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionInterface;

class VersionFinderTest extends TestCase
{
    /**
     * @var TypesConfig
     */
    private static $typesConfig;

    /**
     * @var VersionStorageInterface|ObjectProphecy
     */
    private $versionStorage;

    public static function setUpBeforeClass(): void
    {
        self::$typesConfig = TypesConfigFactory::get();
    }

    public static function tearDownAfterClass(): void
    {
        self::$typesConfig = null;
    }

    public function setUp(): void
    {
        $this->versionStorage = $this->prophesize(VersionStorageInterface::class);
    }

    public function tearDown(): void
    {
        $this->versionStorage = null;
    }

    /**
     * @test
     */
    public function it_find_version_for_resource(): void
    {
        $versions = [$this->createVersion(), $this->createVersion()];

        $this->versionStorage->listForResource(Product::VERSION_TYPE, '1')
            ->shouldBeCalledTimes(1)
            ->willReturn($versions);
        $this->versionStorage->listForResourceList(Argument::cetera())
            ->shouldNotBeCalled();

        $finder = new VersionFinder(self::$typesConfig, $this->versionStorage->reveal());

        self::assertSame(
            $versions,
            $finder->findByResource(new Product('1'))
        );
    }

    /**
     * @test
     */
    public function it_find_version_for_children_resource(): void
    {
        $versions = [$this->createVersion(), $this->createVersion()];

        $this->versionStorage->listForResource(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->versionStorage->listForResourceList([Order::VERSION_TYPE, '1'], [OrderItem::VERSION_TYPE, '1'], [OrderItem::VERSION_TYPE, '2'])
            ->shouldBeCalledTimes(1)
            ->willReturn($versions);

        $finder = new VersionFinder(self::$typesConfig, $this->versionStorage->reveal());

        self::assertSame(
            $versions,
            $finder->findByResource(new Order('1', [new OrderItem('1'), new OrderItem('2')]))
        );
    }

    /**
     * @test
     */
    public function it_find_version_for_author(): void
    {
        $versions = [$this->createVersion(), $this->createVersion()];

        $this->versionStorage->listForAuthor(User::VERSION_TYPE, '1')
            ->shouldBeCalledTimes(1)
            ->willReturn($versions);

        $this->versionStorage->listForAuthorList(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $finder = new VersionFinder(self::$typesConfig, $this->versionStorage->reveal());

        self::assertSame(
            $versions,
            $finder->findByAuthor(new User('1'))
        );
    }

    private function createVersion(): VersionInterface
    {
        return $this->prophesize(VersionInterface::class)->reveal();
    }
}
