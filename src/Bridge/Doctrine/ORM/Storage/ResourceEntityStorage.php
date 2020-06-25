<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Storage;

use Yokai\Versioning\Storage\ChainableResourceStorageInterface;
use Yokai\Versioning\VersionableResourceInterface;

class ResourceEntityStorage implements ChainableResourceStorageInterface
{
    use OrmRegistryTrait;

    /**
     * @inheritdoc
     */
    public function supports(string $class): bool
    {
        return $this->doctrine->getManagerForClass($class) !== null;
    }

    /**
     * @inheritdoc
     */
    public function get(string $class, string $id): ?VersionableResourceInterface
    {
        $author = $this->getManager($class)->find($class, $id);
        if (!$author instanceof VersionableResourceInterface) {
            return null;
        }

        return $author;
    }

    /**
     * @inheritdoc
     */
    public function list(string $class): iterable
    {
        $query = $this->getRepository($class)
            ->createQueryBuilder('e')
            ->getQuery();

        foreach ($query->iterate() as $row) {
            yield $row[0];
        }
    }
}
