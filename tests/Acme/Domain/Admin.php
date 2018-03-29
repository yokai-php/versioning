<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

use Yokai\Versioning\VersionableAuthorInterface;

class Admin implements VersionableAuthorInterface
{
    public const VERSION_TYPE = 'admin';

    use Identifiable;

    public function getVersionableId(): string
    {
        return $this->id;
    }
}
