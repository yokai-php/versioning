<?php

namespace Yokai\Versioning\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yokai\Versioning\Exception\InvalidAuthorException;
use Yokai\Versioning\Exception\InvalidResourceException;
use Yokai\Versioning\Tests\Acme\Domain\Admin;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Tests\Acme\Domain\User;
use Yokai\Versioning\TypesConfig;

class TypesConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_extract_config(): void
    {
        $config = new TypesConfig(
            [
                User::VERSION_TYPE => User::class,
                Admin::VERSION_TYPE => Admin::class,
            ],
            [
                Order::VERSION_TYPE => Order::class,
                Product::VERSION_TYPE => Product::class,
            ]
        );

        self::assertSame(
            [User::class, Admin::class],
            $config->listAuthorClasses()
        );
        self::assertSame(
            [User::VERSION_TYPE, Admin::VERSION_TYPE],
            $config->listAuthorTypes()
        );
        self::assertSame(
            User::VERSION_TYPE,
            $config->getAuthorType(new User('1'))
        );
        self::assertSame(
            Admin::VERSION_TYPE,
            $config->getAuthorType(new Admin('1'))
        );
        self::assertSame(
            User::class,
            $config->getAuthorClass(User::VERSION_TYPE)
        );
        self::assertSame(
            Admin::class,
            $config->getAuthorClass(Admin::VERSION_TYPE)
        );

        self::assertSame(
            [Order::class, Product::class],
            $config->listResourceClasses()
        );
        self::assertSame(
            [Order::VERSION_TYPE, Product::VERSION_TYPE],
            $config->listResourceTypes()
        );
        self::assertSame(
            Order::VERSION_TYPE,
            $config->getResourceType(new Order('1', [new OrderItem('1')]))
        );
        self::assertSame(
            Product::VERSION_TYPE,
            $config->getResourceType(new Product('1'))
        );
        self::assertSame(
            Order::class,
            $config->getResourceClass(Order::VERSION_TYPE)
        );
        self::assertSame(
            Product::class,
            $config->getResourceClass(Product::VERSION_TYPE)
        );
    }

    /**
     * @test
     */
    public function it_throw_exception(): void
    {
        $config = new TypesConfig(
            [
                User::VERSION_TYPE => User::class,
            ],
            [
                Order::VERSION_TYPE => Order::class,
            ]
        );

        $exceptionCaught = [];

        $authors = [new User('1'), new Admin('1')];
        foreach ($authors as $author) {
            try {
                $config->getAuthorType($author);
            } catch (InvalidAuthorException $exception) {
                $exceptionCaught[] = $exception;
            }
        }

        $authorTypes = [User::VERSION_TYPE, Admin::VERSION_TYPE];
        foreach ($authorTypes as $type) {
            try {
                $config->getAuthorClass($type);
            } catch (InvalidAuthorException $exception) {
                $exceptionCaught[] = $exception;
            }
        }

        $resources = [new Order('1', [new OrderItem('1')]), new Product('1')];
        foreach ($resources as $resource) {
            try {
                $config->getResourceType($resource);
            } catch (InvalidResourceException $exception) {
                $exceptionCaught[] = $exception;
            }
        }

        $resourceTypes = [Order::VERSION_TYPE, Product::VERSION_TYPE];
        foreach ($resourceTypes as $type) {
            try {
                $config->getResourceClass($type);
            } catch (InvalidResourceException $exception) {
                $exceptionCaught[] = $exception;
            }
        }

        self::assertCount(4, $exceptionCaught);
        self::assertSame(
            [
                'Unknown author class "'.Admin::class.'".',
                'Unknown author type "'.Admin::VERSION_TYPE.'".',
                'Unknown resource class "'.Product::class.'".',
                'Unknown resource type "'.Product::VERSION_TYPE.'".',
            ],
            array_map(function (\Exception $e) { return $e->getMessage(); }, $exceptionCaught)
        );
    }
}
