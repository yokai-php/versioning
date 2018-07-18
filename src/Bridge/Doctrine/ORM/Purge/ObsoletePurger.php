<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Purge;

use Doctrine\Common\Persistence\ManagerRegistry;
use Throwable;
use Yokai\Versioning\Purge\ChainablePurgerInterface;

/**
 * FIXME not working
 */
class ObsoletePurger implements ChainablePurgerInterface
{
    use PurgerTrait;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @inheritDoc
     */
    public function purge(): int
    {
        $manager = $this->getManager();

        $removals = $this->findResourcesRemovals();

        $manager->beginTransaction();

        $count = 0;

        try {
            foreach ($removals as $resourceClass => $resourceIds) {
                $count += $this->deleteResourceVersions($resourceClass, $resourceIds);
            }
            $manager->commit();
        } catch (Throwable $exception) {
            $manager->rollback();

            throw $exception;
        }

        return $count;
    }

    private function findResourcesRemovals(): array
    {
        $removals = [];

        foreach ($this->findGroupedResourcesIds() as $resourceClass => $resourceIds) {
            $query = $this->queryBuilder($resourceClass, 'resource');
            $query
                ->select('resource.id')
                ->where('resource.id IN (:ids)')
                ->setParameter('ids', $resourceIds);
            $foundIds = array_column(
                $query->getQuery()->getArrayResult(),
                'id'
            );

            sort($resourceIds);
            sort($foundIds);

            $removals[$resourceClass] = array_diff($resourceIds, $foundIds);
        }

        return $removals;
    }

    private function deleteResourceVersions(string $resourceClass, array $resourceIds): int
    {
        if (count($resourceIds) === 0) {
            return 0;
        }

        $query = $this->deleteVersionQueryBuilder()
            ->where('version.resourceClass = :class')
            ->andWhere('version.resourceId IN(:ids)')
            ->setParameters(['class' => $resourceClass, 'ids' => $resourceIds])
            ->getQuery();

        return intval($query->execute());
    }

    private function findGroupedResourcesIds(): array
    {
        $classesQuery = $this->versionQueryBuilder();
        $classesQuery->select('DISTINCT version.resourceClass');
        $classes = array_column(
            $classesQuery->getQuery()->getArrayResult(),
            'resourceClass'
        );

        $idsByClass = [];

        foreach ($classes as $class) {
            $idsQuery = $this->versionQueryBuilder();
            $idsQuery
                ->select('DISTINCT version.resourceId')
                ->where('version.resourceClass = :class')
                ->setParameter('class', $class);
            $idsByClass[$class] = array_column(
                $idsQuery->getQuery()->getArrayResult(),
                'resourceId'
            );
        }

        return $idsByClass;
    }
}
