<?php declare(strict_types=1);

namespace Yokai\Versioning\Initialize;

interface ObjectFinderInterface
{
    /**
     * @param string $class
     *
     * @return iterable|object[]
     */
    public function find(string $class): iterable;
}
