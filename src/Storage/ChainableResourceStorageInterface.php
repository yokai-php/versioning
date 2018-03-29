<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

interface ChainableResourceStorageInterface extends ResourceStorageInterface
{
    /**
     * @param string $class
     *
     * @return bool
     */
    public function supports(string $class): bool;
}
