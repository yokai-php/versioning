Custom author storage
---------------------

Writing a custom author storage is as easy as 
creating a class that implements the `Yokai\Versioning\Storage\AuthorStorageInterface` interface.

```php
<?php

use Yokai\Versioning\Storage\ChainableAuthorStorageInterface;
use Yokai\Versioning\VersionableAuthorInterface;

class InMemoryAuthorStorage implements ChainableAuthorStorageInterface
{
    private $storage = [];

    public function supports(string $class): bool
    {
        return in_array($class, ['Acme\\User', 'Acme\\Admin'], true);
    }

    public function get(string $class, string $id): ?VersionableAuthorInterface
    {
        if (!isset($this->storage[$class][$id])) {
            return null;
        }

        return $this->storage[$class][$id];
    }
}
```

> **note** you may noticed that this class is implementing `Yokai\Versioning\Storage\ChainableAuthorStorageInterface`
which is a super interface that contains a `support`.
This interface is very convenient if your authors belongs to different storage.


---

« [README](../../README.md)
