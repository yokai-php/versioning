Custom version storage
----------------------

Writing a custom version storage is as easy as 
creating a class that implements the `Yokai\Versioning\Storage\VersionStorageInterface` interface.

```php
<?php

use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\VersionInterface;

class InMemoryVersionStorage implements VersionStorageInterface
{
    /**
     * @var VersionInterface[]
     */
    private $storage = [];

    public function store($versions): void
    {
        foreach ($versions as $version) {
            $this->storage[] = $version;
        }
    }

    public function currentForResource(string $type, string $id): ?VersionInterface
    {
        $versions = $this->listForResource($type, $id);
        if (count($versions) === 0) {
            return null;
        }

        $sort = function (VersionInterface $versionA, VersionInterface $versionB) {
            return $versionA->getVersion() <=> $versionB->getVersion();
        };
        usort($versions, $sort);

        return end($versions);
    }

    public function listForResource(string $type, string $id): iterable
    {
        $filter = function (VersionInterface $version) use ($type, $id) {
            return $version->getResourceType() === $type && $version->getResourceId() === $id;
        };

        return array_filter($this->storage, $filter);
    }

    public function listForResourceList(array ...$resources): iterable
    {
        $versions = [];

        foreach ($resources as list($resourceType, $resourceId)) {
            foreach ($this->listForResource($resourceType, $resourceId) as $version) {
                $versions[] = $version;
            }
        }

        return $versions;
    }

    public function listForAuthor(string $type, string $id): iterable
    {
        $filter = function (VersionInterface $version) use ($type, $id) {
            return $version->getAuthorType() === $type && $version->getAuthorId() === $id;
        };

        return array_filter($this->storage, $filter);
    }

    public function listForAuthorList(array ...$authors): iterable
    {
        $versions = [];

        foreach ($authors as list($authorType, $authorId)) {
            foreach ($this->listForAuthor($authorType, $authorId) as $version) {
                $versions[] = $version;
            }
        }

        return $versions;
    }
}
```


---

« [README](../../README.md)
