<?php declare(strict_types=1);

namespace Yokai\Versioning;

use Yokai\Versioning\Exception\InvalidAuthorException;
use Yokai\Versioning\Exception\InvalidResourceException;

class TypesConfig
{
    /**
     * @var array
     */
    private $authorClassByType;

    /**
     * @var array
     */
    private $authorTypeByClass;

    /**
     * @var array
     */
    private $resourceClassByType;

    /**
     * @var array
     */
    private $resourceTypeByClass;

    public function __construct(array $authorMap, array $resourceMap)
    {
        $this->authorClassByType = $authorMap;
        $this->authorTypeByClass = array_flip($authorMap);
        $this->resourceClassByType = $resourceMap;
        $this->resourceTypeByClass = array_flip($resourceMap);
    }

    public function listAuthorClasses(): array
    {
        return array_keys($this->authorTypeByClass);
    }

    public function listAuthorTypes(): array
    {
        return array_keys($this->authorClassByType);
    }

    public function getAuthorClass(string $type): string
    {
        if (!isset($this->authorClassByType[$type])) {
            throw InvalidAuthorException::unknownType($type);
        }

        return $this->authorClassByType[$type];
    }

    public function getAuthorType(VersionableAuthorInterface $author): string
    {
        $class = get_class($author);
        if (!isset($this->authorTypeByClass[$class])) {
            throw InvalidAuthorException::unknownClass($class);
        }

        return $this->authorTypeByClass[$class];
    }

    public function listResourceClasses(): array
    {
        return array_keys($this->resourceTypeByClass);
    }

    public function listResourceTypes(): array
    {
        return array_keys($this->resourceClassByType);
    }

    public function getResourceClass(string $type): string
    {
        if (!isset($this->resourceClassByType[$type])) {
            throw InvalidResourceException::unknownType($type);
        }

        return $this->resourceClassByType[$type];
    }

    public function getResourceType(VersionableResourceInterface $resource): string
    {
        $class = get_class($resource);
        if (!isset($this->resourceTypeByClass[$class])) {
            throw InvalidResourceException::unknownClass($class);
        }

        return $this->resourceTypeByClass[$class];
    }
}
