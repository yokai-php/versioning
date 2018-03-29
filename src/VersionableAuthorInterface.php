<?php declare(strict_types=1);

namespace Yokai\Versioning;

interface VersionableAuthorInterface
{
    /**
     * @return string
     */
    public function getVersionableId(): string;
}
