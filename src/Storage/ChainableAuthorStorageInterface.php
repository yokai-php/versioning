<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

interface ChainableAuthorStorageInterface extends AuthorStorageInterface
{
    /**
     * @param string $class
     *
     * @return bool
     */
    public function supports(string $class): bool;
}
