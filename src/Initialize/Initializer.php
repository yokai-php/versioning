<?php declare(strict_types=1);

namespace Yokai\Versioning\Initialize;

use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\UpdateGuesser\UpdateGuesserInterface;
use Yokai\Versioning\VersionBuilder;

class Initializer
{
    /**
     * @var ObjectFinderInterface
     */
    private $objectFinder;

    /**
     * @var UpdateGuesserInterface
     */
    private $updateGuesser;

    /**
     * @var VersionBuilder
     */
    private $versionBuilder;

    /**
     * @var VersionStorageInterface
     */
    private $versionStorage;

    public function __construct(
        ObjectFinderInterface $objectFinder,
        UpdateGuesserInterface $updateGuesser,
        VersionBuilder $versionBuilder,
        VersionStorageInterface $versionStorage
    ) {
        $this->objectFinder = $objectFinder;
        $this->updateGuesser = $updateGuesser;
        $this->versionBuilder = $versionBuilder;
        $this->versionStorage = $versionStorage;
    }

    public function initialize(string $class): void
    {
        $versions = [];
        $objects = $this->objectFinder->find($class);

        foreach ($objects as $object) {
            $resources = $this->updateGuesser->guessUpdates($object, UpdateGuesserInterface::ACTION_INSERT);

            foreach ($resources as $resource) {
                $versions[] = $this->versionBuilder->build($resource);
            }
        }

        if (count($versions) > 0) {
            $this->versionStorage->store($versions);
        }
    }
}
