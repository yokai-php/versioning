<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Storage;

use Yokai\Versioning\Bridge\Doctrine\ORM\Entity\Version;
use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\VersionInterface;

class VersionEntityStorage implements VersionStorageInterface
{
    use OrmRegistryTrait;

    /**
     * @inheritDoc
     */
    public function store($versions): void
    {
        $manager = $this->getManager(Version::class);
        $uow = $manager->getUnitOfWork();

        if ($versions instanceof VersionInterface) {
            $versions = [$versions];
        }

        foreach ($versions as $version) {
            $manager->persist($version);
            $uow->computeChangeSet($manager->getClassMetadata(Version::class), $version);
        }

        $manager->flush();

        foreach ($versions as $version) {
            $manager->detach($version);
        }
    }

    /**
     * @inheritDoc
     */
    public function currentForResource(string $type, string $id): ?VersionInterface
    {
        $version = $this->getRepository(Version::class)->findOneBy(
            ['resourceType' => $type, 'resourceId' => $id],
            ['version' => 'desc']
        );

        if (!$version instanceof VersionInterface) {
            return null;
        }

        return $version;
    }

    /**
     * @inheritDoc
     */
    public function listForResource(string $type, string $id): iterable
    {
        return $this->getRepository(Version::class)->findBy(
            ['resourceType' => $type, 'resourceId' => $id],
            ['version' => 'desc']
        );
    }

    /**
     * @inheritDoc
     */
    public function listForResourceList(array ...$resources): iterable
    {
        $query = $this->getRepository(Version::class)->createQueryBuilder('version')
            ->orderBy('version.version', 'desc');

        foreach ($resources as $idx => list($type, $id)) {
            $typeParameter = sprintf('type_%d', $idx);
            $idParameter = sprintf('id_%d', $idx);

            $query
                ->orWhere(
                    $query->expr()->andX(
                        sprintf('version.resourceType = :%s', $typeParameter),
                        sprintf('version.resourceId = :%s', $idParameter)
                    )
                )
                ->setParameter($typeParameter, $type)
                ->setParameter($idParameter, $id)
            ;
        }

        return $query->getQuery()->execute();
    }

    /**
     * @inheritDoc
     */
    public function listForAuthor(string $type, string $id): iterable
    {
        return $this->getRepository(Version::class)->findBy(
            ['authorType' => $type, 'authorId' => $id],
            ['loggedAt' => 'desc']
        );
    }

    /**
     * @inheritDoc
     */
    public function listForAuthorList(array ...$authors): iterable
    {
        $query = $this->getRepository(Version::class)->createQueryBuilder('version')
            ->orderBy('version.loggedAt', 'desc');

        foreach ($authors as $idx => list($type, $id)) {
            $typeParameter = sprintf('type_%d', $idx);
            $idParameter = sprintf('id_%d', $idx);

            $query
                ->orWhere(
                    $query->expr()->andX(
                        sprintf('version.authorType = :%s', $typeParameter),
                        sprintf('version.authorId = :%s', $idParameter)
                    )
                )
                ->setParameter($typeParameter, $type)
                ->setParameter($idParameter, $id)
            ;
        }

        return $query->getQuery()->execute();
    }
}
