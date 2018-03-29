<?php

namespace Yokai\Versioning\Finder;

use Yokai\Versioning\Storage\ResourceStorageInterface;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionableResourceInterface;
use Yokai\Versioning\VersionInterface;

class ResourceFinder
{
    /**
     * @var TypesConfig
     */
    private $typesConfig;

    /**
     * @var ResourceStorageInterface
     */
    private $resourceStorage;

    public function __construct(TypesConfig $typesConfig, ResourceStorageInterface $resourceStorage)
    {
        $this->typesConfig = $typesConfig;
        $this->resourceStorage = $resourceStorage;
    }

    /**
     * @param VersionInterface $version
     *
     * @return VersionableResourceInterface|null
     */
    public function findForVersion(VersionInterface $version): ?VersionableResourceInterface
    {
        $resourceType = $version->getResourceType();
        $resourceId = $version->getResourceId();
        if (null === $resourceType || null === $resourceId) {
            return null;
        }

        return $this->resourceStorage->get($this->typesConfig->getResourceClass($resourceType), $resourceId);
    }
}
