<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

use Yokai\Versioning\VersionableParentInterface;
use Yokai\Versioning\VersionableResourceInterface;

class Order implements VersionableParentInterface
{
    public const VERSION_TYPE = 'order';

    use Identifiable {
        Identifiable::__construct as private __idConstruct;
    }

    /**
     * @var OrderItem[]
     */
    private $items;

    /**
     * @param string      $id
     * @param OrderItem[] $items
     */
    public function __construct(string $id, array $items)
    {
        $this->__idConstruct($id);
        $this->items = $items;
        foreach ($items as $item) {
            $item->order = $this;
        }
    }

    public function getVersionableId(): string
    {
        return $this->id;
    }

    public function getVersionableChildren(): iterable
    {
        return $this->items;
    }
}
