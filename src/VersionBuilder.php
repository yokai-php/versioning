<?php declare(strict_types=1);

namespace Yokai\Versioning;

use Yokai\Versioning\Storage\VersionStorageInterface;

class VersionBuilder
{
    /**
     * @var TypesConfig
     */
    private $typesConfig;

    /**
     * @var VersionStorageInterface
     */
    private $versionStorage;

    /**
     * @var SnapshotTakerInterface
     */
    private $snapshotTaker;

    /**
     * @var ChangesetBuilder
     */
    private $changesetBuilder;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var VersionFactoryInterface
     */
    private $versionFactory;

    public function __construct(
        TypesConfig $typesConfig,
        VersionStorageInterface $versionStorage,
        SnapshotTakerInterface $snapshotTaker,
        ChangesetBuilder $changesetBuilder,
        Context $context,
        VersionFactoryInterface $versionFactory
    ) {
        $this->typesConfig = $typesConfig;
        $this->versionStorage = $versionStorage;
        $this->snapshotTaker = $snapshotTaker;
        $this->changesetBuilder = $changesetBuilder;
        $this->context = $context;
        $this->versionFactory = $versionFactory;
    }

    /**
     * Build a version for a versionable entity
     *
     * @param VersionableResourceInterface $resource
     *
     * @return VersionInterface
     */
    public function build(VersionableResourceInterface $resource): VersionInterface
    {
        $resourceType = $this->typesConfig->getResourceType($resource);
        $resourceId = $resource->getVersionableId();

        $versionNumber = 1;
        $previousSnapshot = [];

        // try to find previous version of the resource
        $previousVersion = $this->versionStorage->currentForResource($resourceType, $resourceId);

        // if previous version was found, extract version number and snapshot from it
        if (null !== $previousVersion) {
            $versionNumber = $previousVersion->getVersion() + 1;
            $previousSnapshot = $previousVersion->getSnapshot();
        }

        // take a snapshot of resource using normalization
        $snapshot = $this->snapshotTaker->take($resource);

        // compare previous snapshot with the one we just took to determiner change set
        $changeSet = $this->changesetBuilder->build($previousSnapshot, $snapshot);

        $authorType = null;
        $authorId = null;
        if (null !== $author = $this->context->getAuthor()) {
            $authorType = $this->typesConfig->getAuthorType($author);
            $authorId = $author->getVersionableId();
        }

        return $this->versionFactory->create(
            [$resourceType, $resourceId],
            $versionNumber,
            $snapshot,
            $changeSet,
            [$authorType, $authorId],
            [$this->context->getEntryPoint(), $this->context->getParameters()],
            Time::immutable()
        );
    }
}
