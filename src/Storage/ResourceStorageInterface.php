<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

use Yokai\Versioning\VersionableResourceInterface;

interface ResourceStorageInterface
{
    /**
     * @param string $class
     * @param string $id
     *
     * @return VersionableResourceInterface|null
     */
    public function get(string $class, string $id): ?VersionableResourceInterface;
}
