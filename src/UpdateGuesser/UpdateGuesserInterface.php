<?php declare(strict_types=1);

namespace Yokai\Versioning\UpdateGuesser;

use Yokai\Versioning\VersionableResourceInterface;

interface UpdateGuesserInterface
{
    const ACTION_INSERT = 'insert';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * Get updated entities
     *
     * @param object $entity
     * @param string $action
     *
     * @return VersionableResourceInterface[]|iterable
     */
    public function guessUpdates(object $entity, string $action): iterable;
}
