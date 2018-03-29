<?php

namespace Yokai\Versioning\Tests\Unit\Initialize;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Initialize\Initializer;
use Yokai\Versioning\Initialize\ObjectFinderInterface;
use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\UpdateGuesser\UpdateGuesserInterface;
use Yokai\Versioning\VersionBuilder;
use Yokai\Versioning\VersionInterface;

class InitializerTest extends TestCase
{
    /**
     * @var ObjectFinderInterface|ObjectProphecy
     */
    private $objectFinder;

    /**
     * @var UpdateGuesserInterface|ObjectProphecy
     */
    private $updateGuesser;

    /**
     * @var VersionBuilder|ObjectProphecy
     */
    private $versionBuilder;

    /**
     * @var VersionStorageInterface|ObjectProphecy
     */
    private $versionStorage;

    protected function setUp(): void
    {
        $this->objectFinder = $this->prophesize(ObjectFinderInterface::class);
        $this->updateGuesser = $this->prophesize(UpdateGuesserInterface::class);
        $this->versionBuilder = $this->prophesize(VersionBuilder::class);
        $this->versionStorage = $this->prophesize(VersionStorageInterface::class);
    }

    protected function tearDown(): void
    {
        $this->objectFinder =
        $this->updateGuesser =
        $this->versionBuilder =
        $this->versionStorage = null;
    }

    private function initialize(string $class)
    {
        $initializer = new Initializer(
            $this->objectFinder->reveal(),
            $this->updateGuesser->reveal(),
            $this->versionBuilder->reveal(),
            $this->versionStorage->reveal()
        );

        $initializer->initialize($class);
    }

    /**
     * @test
     */
    public function it_store_no_versions_if_no_object_found(): void
    {
        $this->objectFinder->find(Product::class)->shouldBeCalledTimes(1)->willReturn([]);

        $this->updateGuesser->guessUpdates(Argument::any(), UpdateGuesserInterface::ACTION_INSERT)
            ->shouldNotBeCalled();

        $this->versionBuilder->build(Argument::any())->shouldNotBeCalled();
        $this->versionStorage->store(Argument::any())->shouldNotBeCalled(1);

        $this->initialize(Product::class);
    }

    /**
     * @test
     */
    public function it_store_no_versions_if_no_resource_guessed(): void
    {
        $products = [$product1 = new Product('1'), $product2 = new Product('2')];

        $this->objectFinder->find(Product::class)->shouldBeCalledTimes(1)->willReturn($products);

        $this->updateGuesser->guessUpdates($product1, UpdateGuesserInterface::ACTION_INSERT)
            ->shouldBeCalledTimes(1)
            ->willReturn([]);
        $this->updateGuesser->guessUpdates($product2, UpdateGuesserInterface::ACTION_INSERT)
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $this->versionBuilder->build(Argument::any())->shouldNotBeCalled();
        $this->versionStorage->store(Argument::any())->shouldNotBeCalled(1);

        $this->initialize(Product::class);
    }

    /**
     * @test
     */
    public function it_initialize_versions_for_found_objects(): void
    {
        $version1 = $this->prophesize(VersionInterface::class)->reveal();
        $version2 = $this->prophesize(VersionInterface::class)->reveal();

        $products = [$product1 = new Product('1'), $product2 = new Product('2')];

        $this->objectFinder->find(Product::class)->shouldBeCalledTimes(1)->willReturn($products);

        $this->updateGuesser->guessUpdates($product1, UpdateGuesserInterface::ACTION_INSERT)
            ->shouldBeCalledTimes(1)
            ->willReturn([$product1]);
        $this->updateGuesser->guessUpdates($product2, UpdateGuesserInterface::ACTION_INSERT)
            ->shouldBeCalledTimes(1)
            ->willReturn([$product2]);

        $this->versionBuilder->build($product1)->shouldBeCalledTimes(1)->willReturn($version1);
        $this->versionBuilder->build($product2)->shouldBeCalledTimes(1)->willReturn($version2);

        $this->versionStorage->store([$version1, $version2])->shouldBeCalledTimes(1);

        $this->initialize(Product::class);
    }
}
