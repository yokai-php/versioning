<?php declare(strict_types=1);

namespace Yokai\Versioning;

interface VersionableResourceInterface
{
    /**
     * @return string
     */
    public function getVersionableId(): string;
}
