<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Storage;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

trait OrmRegistryTrait
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function getRepository(string $class): EntityRepository
    {
        return $this->getManager($class)->getRepository($class);
    }

    private function getManager(string $class): EntityManager
    {
        $manager = $this->doctrine->getManagerForClass($class);
        if (!$manager instanceof EntityManager) {
            throw new \LogicException('Expecting ORM manager.');
        }

        return $manager;
    }
}
