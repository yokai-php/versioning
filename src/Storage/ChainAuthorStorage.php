<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

use Yokai\Versioning\Exception\InvalidAuthorException;
use Yokai\Versioning\VersionableAuthorInterface;

class ChainAuthorStorage implements AuthorStorageInterface
{
    /**
     * @var ChainableAuthorStorageInterface[]|iterable
     */
    private $storages;

    public function __construct(iterable $storages)
    {
        $this->storages = $storages;
    }

    /**
     * @inheritDoc
     */
    public function get(string $class, string $id): ?VersionableAuthorInterface
    {
        foreach ($this->storages as $storage) {
            if (!$storage->supports($class)) {
                continue;
            }

            return $storage->get($class, $id);
        }

        throw InvalidAuthorException::unknownClass($class);
    }
}
