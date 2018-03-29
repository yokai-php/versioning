<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

use Yokai\Versioning\Exception\InvalidResourceException;
use Yokai\Versioning\VersionableResourceInterface;

class ChainResourceStorage implements ResourceStorageInterface
{
    /**
     * @var ChainableResourceStorageInterface[]|iterable
     */
    private $storages;

    public function __construct(iterable $storages)
    {
        $this->storages = $storages;
    }

    /**
     * @inheritDoc
     */
    public function get(string $class, string $id): ?VersionableResourceInterface
    {
        foreach ($this->storages as $storage) {
            if (!$storage->supports($class)) {
                continue;
            }

            return $storage->get($class, $id);
        }

        throw InvalidResourceException::unknownClass($class);
    }
}
