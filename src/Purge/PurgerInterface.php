<?php declare(strict_types=1);

namespace Yokai\Versioning\Purge;

interface PurgerInterface
{
    /**
     * Purge versions.
     *
     * @return int
     */
    public function purge(): int;
}
