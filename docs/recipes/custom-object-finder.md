Custom object finder
--------------------

Writing a custom object finder is as easy as 
creating a class that implements the `Yokai\Versioning\Initialize\ObjectFinderInterface` interface.

```php
<?php

use Yokai\Versioning\Initialize\ChainableObjectFinderInterface;

class InMemoryObjectFinder implements ChainableObjectFinderInterface
{
    /**
     * @var array
     */
    private $storage = [];

    public function add(object $object): void
    {
        if (!isset($this->storage[get_class($object)])) {
            $this->storage[get_class($object)] = [];
        }

        $this->storage[get_class($object)][] = $object;
    }

    public function supports(string $class): bool
    {
        return isset($this->storage[$class]);
    }

    public function find(string $class): iterable
    {
        return $this->storage[$class] ?? [];
    }
}
```

> **note** you may noticed that this class is implementing `Yokai\Versioning\Storage\ChainableObjectFinderInterface`
which is a super interface that contains a `support`.
This interface is very convenient if your objects belongs to different storage.

> **note** the `add` method is not part of the interface, it was added for as a convenient way to fill the storage.


---

« [README](../../README.md)
