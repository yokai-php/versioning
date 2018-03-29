<?php declare(strict_types=1);

namespace Yokai\Versioning\Storage;

use Yokai\Versioning\VersionInterface;

interface VersionStorageInterface
{
    /**
     * @param iterable|VersionInterface[]|VersionInterface $versions
     *
     * @return void
     */
    public function store($versions): void;

    /**
     * @param string $type
     * @param string $id
     *
     * @return null|VersionInterface
     */
    public function currentForResource(string $type, string $id): ?VersionInterface;

    /**
     * @param string $type
     * @param string $id
     *
     * @return VersionInterface[]|iterable
     */
    public function listForResource(string $type, string $id): iterable;

    /**
     * @param array[] ...$resources
     *
     * @return VersionInterface[]|iterable
     */
    public function listForResourceList(array ...$resources): iterable;

    /**
     * @param string $type
     * @param string $id
     *
     * @return VersionInterface[]|iterable
     */
    public function listForAuthor(string $type, string $id): iterable;

    /**
     * @param array[] ...$authors
     *
     * @return VersionInterface[]|iterable
     */
    public function listForAuthorList(array ...$authors): iterable;
}
