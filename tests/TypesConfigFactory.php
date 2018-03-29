<?php

namespace Yokai\Versioning\Tests;

use Yokai\Versioning\Tests\Acme\Domain\Admin;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Tests\Acme\Domain\User;
use Yokai\Versioning\TypesConfig;

class TypesConfigFactory
{
    /**
     * @var TypesConfig|null
     */
    private static $instance;

    public static function get(): TypesConfig
    {
        if (self::$instance === null) {
            self::$instance = new TypesConfig(
                [
                    User::VERSION_TYPE => User::class,
                    Admin::VERSION_TYPE => Admin::class,
                ],
                [
                    Order::VERSION_TYPE => Order::class,
                    OrderItem::VERSION_TYPE => OrderItem::class,
                    Product::VERSION_TYPE => Product::class,
                ]
            );
        }

        return self::$instance;
    }
}
