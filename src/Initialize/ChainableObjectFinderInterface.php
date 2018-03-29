<?php declare(strict_types=1);

namespace Yokai\Versioning\Initialize;

interface ChainableObjectFinderInterface extends ObjectFinderInterface
{
    /**
     * @param string $class
     *
     * @return bool
     */
    public function supports(string $class): bool;
}
