<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Storage;

use Yokai\Versioning\Storage\ChainableAuthorStorageInterface;
use Yokai\Versioning\VersionableAuthorInterface;

class AuthorEntityStorage implements ChainableAuthorStorageInterface
{
    use OrmRegistryTrait;

    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $this->doctrine->getManagerForClass($class) !== null;
    }

    /**
     * @inheritDoc
     */
    public function get(string $class, string $id): ?VersionableAuthorInterface
    {
        $author = $this->getManager($class)->find($class, $id);
        if (!$author instanceof VersionableAuthorInterface) {
            return null;
        }

        return $author;
    }
}
