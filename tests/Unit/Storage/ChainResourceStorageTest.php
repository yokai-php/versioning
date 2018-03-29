<?php

namespace Yokai\Versioning\Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Exception\InvalidResourceException;
use Yokai\Versioning\Storage\ChainableResourceStorageInterface;
use Yokai\Versioning\Storage\ChainResourceStorage;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\Product;

class ChainResourceStorageTest extends TestCase
{
    /**
     * @test
     */
    public function it_list_resources_delegating_to_other_storages(): void
    {
        /** @var ChainableResourceStorageInterface|ObjectProphecy $productStorage */
        $productStorage = $this->prophesize(ChainableResourceStorageInterface::class);
        $productStorage->supports(Argument::any())->will(function (array $args) { return $args[0] === Product::class; });
        $productStorage->get(Product::class, Argument::any())->will(function (array $args) { return new Product($args[1]); });

        /** @var ChainableResourceStorageInterface|ObjectProphecy $orderStorage */
        $orderStorage = $this->prophesize(ChainableResourceStorageInterface::class);
        $orderStorage->supports(Argument::any())->will(function (array $args) { return $args[0] === Order::class; });
        $orderStorage->get(Order::class, Argument::any())->will(function (array $args) { return new Order($args[1], []); });

        $storage = new ChainResourceStorage([$productStorage->reveal(), $orderStorage->reveal()]);

        /** @var Product $product1 */
        $product1 = $storage->get(Product::class, '1');
        self::assertInstanceOf(Product::class, $product1);
        self::assertSame('1', $product1->id);
        /** @var Product $product2 */
        $product2 = $storage->get(Product::class, '2');
        self::assertInstanceOf(Product::class, $product2);
        self::assertSame('2', $product2->id);

        /** @var Order $order1 */
        $order1 = $storage->get(Order::class, '1');
        self::assertInstanceOf(Order::class, $order1);
        self::assertSame('1', $order1->id);
        /** @var Order $order2 */
        $order2 = $storage->get(Order::class, '2');
        self::assertInstanceOf(Order::class, $order2);
        self::assertSame('2', $order2->id);

        $exceptionThrown = null;
        try {
            $storage->get('Class\\That\\Do\\Not\\Exist', '1');
        } catch (InvalidResourceException $e) {
            $exceptionThrown = $e;
        }

        self::assertInstanceOf(InvalidResourceException::class, $exceptionThrown);
        self::assertSame('Unknown resource class "Class\\That\\Do\\Not\\Exist".', $exceptionThrown->getMessage());
    }
}
