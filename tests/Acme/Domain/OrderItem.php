<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

use Yokai\Versioning\VersionableResourceInterface;
use Yokai\Versioning\VersionableChildInterface;

class OrderItem implements VersionableChildInterface
{
    public const VERSION_TYPE = 'order-item';

    use Identifiable;

    /**
     * @var Order
     */
    public $order;

    public function getVersionableParent(): VersionableResourceInterface
    {
        return $this->order;
    }

    public function getVersionableId(): string
    {
        return $this->id;
    }
}
