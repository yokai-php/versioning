<?php declare(strict_types=1);

namespace Yokai\Versioning;

interface VersionableParentInterface extends VersionableResourceInterface
{
    /**
     * @return VersionableResourceInterface[]|iterable
     */
    public function getVersionableChildren(): iterable;
}
