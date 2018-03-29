<?php declare(strict_types=1);

namespace Yokai\Versioning;

use DateTimeInterface;

interface VersionFactoryInterface
{
    /**
     * @param array             $resource
     * @param int               $version
     * @param array             $snapshot
     * @param array             $changeSet
     * @param array             $author
     * @param array             $context
     * @param DateTimeInterface $loggedAt
     *
     * @return VersionInterface
     */
    public function create(
        array $resource,
        int $version,
        array $snapshot,
        array $changeSet,
        array $author,
        array $context,
        DateTimeInterface $loggedAt
    ): VersionInterface;
}
