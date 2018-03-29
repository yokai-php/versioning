<?php declare(strict_types=1);

namespace Yokai\Versioning\Purge;

class ChainPurger implements PurgerInterface
{
    /**
     * @var ChainablePurgerInterface[]|iterable
     */
    private $purgers;

    /**
     * @param ChainablePurgerInterface[]|iterable $purgers
     */
    public function __construct(iterable $purgers)
    {
        $this->purgers = $purgers;
    }

    /**
     * @inheritDoc
     */
    public function purge(): int
    {
        $count = 0;

        foreach ($this->purgers as $purger) {
            $count += $purgerCount = $purger->purge();
        }

        return $count;
    }
}
