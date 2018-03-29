<?php declare(strict_types=1);

namespace Yokai\Versioning\Finder;

use Yokai\Versioning\Storage\AuthorStorageInterface;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionableAuthorInterface;
use Yokai\Versioning\VersionInterface;

class AuthorFinder
{
    /**
     * @var TypesConfig
     */
    private $typesConfig;

    /**
     * @var AuthorStorageInterface
     */
    private $authorStorage;

    public function __construct(TypesConfig $typesConfig, AuthorStorageInterface $authorStorage)
    {
        $this->typesConfig = $typesConfig;
        $this->authorStorage = $authorStorage;
    }

    /**
     * @param VersionInterface $version
     *
     * @return VersionableAuthorInterface|null
     */
    public function findForVersion(VersionInterface $version): ?VersionableAuthorInterface
    {
        $authorType = $version->getAuthorType();
        $authorId = $version->getAuthorId();
        if (null === $authorType || null === $authorId) {
            return null;
        }

        return $this->authorStorage->get($this->typesConfig->getAuthorClass($authorType), $authorId);
    }
}
