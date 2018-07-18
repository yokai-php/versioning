<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Purge;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Yokai\Versioning\Bridge\Doctrine\ORM\Entity\Version;

trait PurgerTrait
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    private function deleteVersionQueryBuilder(string $alias = 'version'): QueryBuilder
    {
        return $this->getManager()->createQueryBuilder()
            ->delete(Version::class, $alias);
    }

    private function versionQueryBuilder(string $alias = 'version'): QueryBuilder
    {
        return $this->queryBuilder(Version::class, $alias);
    }

    private function queryBuilder(string $class, string $alias): QueryBuilder
    {
        return $this->getManager()->createQueryBuilder()
            ->select($alias)
            ->from($class, $alias);
    }

    private function getManager(): EntityManagerInterface
    {
        $manager = $this->doctrine->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException('Expecting ORM manager.');
        }

        return $manager;
    }
}
