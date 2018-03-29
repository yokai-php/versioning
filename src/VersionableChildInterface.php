<?php declare(strict_types=1);

namespace Yokai\Versioning;

interface VersionableChildInterface extends VersionableResourceInterface
{
    /**
     * @return VersionableResourceInterface
     */
    public function getVersionableParent(): VersionableResourceInterface;
}
