<?php declare(strict_types=1);

namespace Yokai\Versioning\UpdateGuesser;

use Yokai\Versioning\VersionableResourceInterface;

class VersionableUpdateGuesser implements ChainableUpdateGuesserInterface
{
    /**
     * @inheritDoc
     */
    public function supportAction(string $action): bool
    {
        return in_array($action, [self::ACTION_INSERT, self::ACTION_UPDATE]);
    }

    /**
     * @inheritDoc
     */
    public function guessUpdates(object $entity, string $action): iterable
    {
        $pendings = [];

        if ($entity instanceof VersionableResourceInterface) {
            $pendings[] = $entity;
        }

        return $pendings;
    }
}
