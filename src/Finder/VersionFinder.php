<?php declare(strict_types=1);

namespace Yokai\Versioning\Finder;

use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionableAuthorInterface;
use Yokai\Versioning\VersionableParentInterface;
use Yokai\Versioning\VersionableResourceInterface;
use Yokai\Versioning\VersionInterface;

class VersionFinder
{
    /**
     * @var TypesConfig
     */
    private $typesConfig;

    /**
     * @var VersionStorageInterface
     */
    private $storage;

    public function __construct(TypesConfig $typesConfig, VersionStorageInterface $storage)
    {
        $this->typesConfig = $typesConfig;
        $this->storage = $storage;
    }

    /**
     * @param VersionableResourceInterface $resource
     *
     * @return VersionInterface[]|iterable
     */
    public function findByResource(VersionableResourceInterface $resource): iterable
    {
        if (!$resource instanceof VersionableParentInterface) {
            return $this->storage->listForResource(
                $this->typesConfig->getResourceType($resource),
                $resource->getVersionableId()
            );
        }

        $resources = [$resource];

        if ($resource instanceof VersionableParentInterface) {
            foreach ($resource->getVersionableChildren() as $child) {
                $resources[] = $child;
            }
        }

        return call_user_func_array(
            [$this->storage, 'listForResourceList'],
            array_map(
                function (VersionableResourceInterface $resource) {
                    return [$this->typesConfig->getResourceType($resource), $resource->getVersionableId()];
                },
                $resources
            )
        );
    }

    /**
     * @param VersionableAuthorInterface $author
     *
     * @return VersionInterface[]|iterable
     */
    public function findByAuthor(VersionableAuthorInterface $author): iterable
    {
        return $this->storage->listForAuthor(
            $this->typesConfig->getAuthorType($author),
            $author->getVersionableId()
        );
    }
}
