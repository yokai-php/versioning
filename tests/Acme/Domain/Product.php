<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

use Yokai\Versioning\VersionableResourceInterface;

class Product implements VersionableResourceInterface
{
    public const VERSION_TYPE = 'product';

    use Identifiable;

    public function getVersionableId(): string
    {
        return $this->id;
    }
}
