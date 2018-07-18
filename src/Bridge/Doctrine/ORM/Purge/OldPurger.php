<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Purge;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Yokai\Versioning\Purge\ChainablePurgerInterface;

class OldPurger implements ChainablePurgerInterface
{
    use PurgerTrait;

    /**
     * @var string
     */
    private $keepModifier;

    public function __construct(ManagerRegistry $doctrine, string $keepModifier)
    {
        $this->doctrine = $doctrine;
        $this->keepModifier = $keepModifier;
    }

    /**
     * @inheritDoc
     */
    public function purge(): int
    {
        if (0 === count($ids = $this->findVersionIds())) {
            return 0;
        }

        $query = $this->deleteVersionQueryBuilder()
            ->where('version.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();

        return intval($query->execute());
    }

    /**
     * @return array
     */
    private function findVersionIds()
    {
        $limit = new DateTime();
        $limit->modify(sprintf('-%s', $this->keepModifier));

        ($query = $this->versionQueryBuilder())
            ->select('version.id')
            ->where('version.loggedAt < :date')
            ->setParameter('date', $limit)
        ;

        return array_column(
            $query->getQuery()->getArrayResult(),
            'id'
        );
    }
}
