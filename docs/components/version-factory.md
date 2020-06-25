Version Factory
---------------

The [VersionFactoryInterface](../../src/VersionFactoryInterface.php) 
is the interface for version factories.

The purpose of a version factory is to return a concrete [version](version.md) instance.

```php
<?php

use Yokai\Versioning\VersionFactoryInterface;
use Yokai\Versioning\VersionInterface;

class VersionFactory implements VersionFactoryInterface
{
    public function create(
        array $resource,
        int $version,
        array $snapshot,
        array $changeSet,
        array $author,
        array $context,
        DateTimeInterface $loggedAt
    ): VersionInterface {
        list($resourceType, $resourceId) = $resource;
        list($authorType, $authorId) = $author;
        list($contextEndpoint, $contextParameters) = $context;

        return new Acme\My\Version(
            $resourceType,
            $resourceId,
            $version,
            $snapshot,
            $changeSet,
            $authorType,
            $authorId,
            $contextEndpoint,
            $contextParameters,
            $loggedAt
        );
    }
}
```

---

« [README](../../README.md)
