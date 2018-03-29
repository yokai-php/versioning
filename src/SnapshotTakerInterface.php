<?php declare(strict_types=1);

namespace Yokai\Versioning;

interface SnapshotTakerInterface
{
    /**
     * @param VersionableResourceInterface $resource
     *
     * @return array
     */
    public function take(VersionableResourceInterface $resource): array;
}
