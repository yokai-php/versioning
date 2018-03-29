<?php

namespace Yokai\Versioning\Tests\Unit\Initialize;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Initialize\ChainableObjectFinderInterface;
use Yokai\Versioning\Initialize\ChainObjectFinder;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\Product;

class ChainObjectFinderTest extends TestCase
{
    /**
     * @test
     */
    public function it_find_objects_delegating_to_other_finders(): void
    {
        $products = [new Product('1'), new Product('2')];
        $orders = [new Order('1', []), new Order('2', [])];

        /** @var ChainableObjectFinderInterface|ObjectProphecy $productFinder */
        $productFinder = $this->prophesize(ChainableObjectFinderInterface::class);
        $productFinder->supports(Argument::any())->will(function (array $args) { return $args[0] === Product::class; });
        $productFinder->find(Product::class)->will(function () use ($products) { return $products; });

        /** @var ChainableObjectFinderInterface|ObjectProphecy $orderFinder */
        $orderFinder = $this->prophesize(ChainableObjectFinderInterface::class);
        $orderFinder->supports(Argument::any())->will(function (array $args) { return $args[0] === Order::class; });
        $orderFinder->find(Order::class)->will(function () use ($orders) { return $orders; });

        $storage = new ChainObjectFinder([$productFinder->reveal(), $orderFinder->reveal()]);

        self::assertSame($products, $storage->find(Product::class));
        self::assertSame($orders, $storage->find(Order::class));

        $exceptionThrown = null;
        try {
            $storage->find('Class\\That\\Do\\Not\\Exist');
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = $e;
        }

        self::assertInstanceOf(\InvalidArgumentException::class, $exceptionThrown);
        self::assertSame('Unsupported class "Class\\That\\Do\\Not\\Exist".', $exceptionThrown->getMessage());
    }
}
