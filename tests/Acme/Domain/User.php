<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

use Yokai\Versioning\VersionableAuthorInterface;

class User implements VersionableAuthorInterface
{
    public const VERSION_TYPE = 'user';

    use Identifiable;

    public function getVersionableId(): string
    {
        return $this->id;
    }
}
