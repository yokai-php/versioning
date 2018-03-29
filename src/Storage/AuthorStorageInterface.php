<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

use Yokai\Versioning\VersionableAuthorInterface;

interface AuthorStorageInterface
{
    /**
     * @param string $class
     * @param string $id
     *
     * @return VersionableAuthorInterface|null
     */
    public function get(string $class, string $id): ?VersionableAuthorInterface;
}
