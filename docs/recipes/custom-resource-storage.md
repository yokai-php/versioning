Custom author storage
---------------------

Writing a custom resource storage is as easy as 
creating a class that implements the `Yokai\Versioning\Storage\ResourceStorageInterface` interface.

```php
<?php

use Yokai\Versioning\Storage\ChainableResourceStorageInterface;
use Yokai\Versioning\VersionableResourceInterface;

class InMemoryResourceStorage implements ChainableResourceStorageInterface
{
    private $storage = [];

    public function supports(string $class): bool
    {
        return in_array($class, ['Acme\\Resource1', 'Acme\\Resource2'], true);
    }

    public function get(string $class, string $id): ?VersionableResourceInterface
    {
        if (!isset($this->storage[$class][$id])) {
            return null;
        }

        return $this->storage[$class][$id];
    }
}
```

> **note** you may noticed that this class is implementing `Yokai\Versioning\Storage\ChainableResourceStorageInterface`
which is a super interface that contains a `support`.
This interface is very convenient if your resources belongs to different storage.


---

« [README](../../README.md)
