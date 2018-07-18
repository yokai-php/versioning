<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Initialize;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Yokai\Versioning\Initialize\ChainableObjectFinderInterface;

class EntityFinder implements ChainableObjectFinderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        return $this->doctrine->getManagerForClass($class) !== null;
    }

    /**
     * @inheritdoc
     */
    public function find(string $class): iterable
    {
        $query = $this->getManager()->createQueryBuilder()
            ->select('e')
            ->from($class, 'e');

        foreach ($query->getQuery()->iterate() as $row) {
            yield $row[0];
        }
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
