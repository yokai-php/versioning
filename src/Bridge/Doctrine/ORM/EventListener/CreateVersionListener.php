<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\UpdateGuesser\UpdateGuesserInterface;
use Yokai\Versioning\VersionableResourceInterface;
use Yokai\Versioning\VersionBuilder;

class CreateVersionListener implements EventSubscriber
{
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

    /**
     * Store versionable entities for which to create versions within onFlush event
     *
     * @var VersionableResourceInterface[]
     * @internal
     */
    private $versionableEntities = [];

    /**
     * Store versioned entities, avoid infinite loops in postFlush
     *
     * @var string[]
     * @internal
     */
    private $versionedEntities = [];

    public function __construct(
        UpdateGuesserInterface $updateGuesser,
        VersionBuilder $versionBuilder,
        VersionStorageInterface $versionStorage
    ) {
        $this->updateGuesser = $updateGuesser;
        $this->versionBuilder = $versionBuilder;
        $this->versionStorage = $versionStorage;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [Events::onFlush, Events::postFlush];
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        $manager = $event->getEntityManager();
        $uow = $manager->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->scheduledInsert($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->scheduledUpdate($entity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->scheduledDelete($entity);
        }
    }

    public function postFlush(): void
    {
        $versions = [];

        foreach ($this->versionableEntities as $oid => $versionable) {
            // if the object has no id, it has been removed, just skip
            if (!$versionable->getVersionableId()) {
                continue;
            }

            $this->versionedEntities[$oid] = true;

            $version = $this->versionBuilder->build($versionable);

            if (empty($version->getChangeset())) {
                continue;
            }

            $versions[] = $version;
        }

        $this->versionableEntities = [];

        if (count($versions) === 0) {
            return;
        }

        $this->versionStorage->store($versions);
    }

    private function scheduledInsert(object $entity): void
    {
        $this->addVersionableEntities(
            $this->updateGuesser->guessUpdates($entity, UpdateGuesserInterface::ACTION_INSERT)
        );
    }

    private function scheduledUpdate(object $entity): void
    {
        $this->addVersionableEntities(
            $this->updateGuesser->guessUpdates($entity, UpdateGuesserInterface::ACTION_UPDATE)
        );
    }

    private function scheduledDelete(object $entity): void
    {
        $this->addVersionableEntities(
            $this->updateGuesser->guessUpdates($entity, UpdateGuesserInterface::ACTION_DELETE)
        );
    }

    private function addVersionableEntities(iterable $resources): void
    {
        foreach ($resources as $versionable) {
            $oid = spl_object_hash($versionable);
            if (!isset($this->versionableEntities[$oid]) && !isset($this->versionedEntities[$oid])) {
                $this->versionableEntities[$oid] = $versionable;
            }
        }
    }
}
