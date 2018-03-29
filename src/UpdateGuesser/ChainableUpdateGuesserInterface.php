<?php declare(strict_types=1);

namespace Yokai\Versioning\UpdateGuesser;

interface ChainableUpdateGuesserInterface extends UpdateGuesserInterface
{
    /**
     * Check if the guesser support the action
     *
     * @param string $action
     *
     * @return bool
     */
    public function supportAction(string $action): bool;
}
