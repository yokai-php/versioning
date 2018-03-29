<?php declare(strict_types=1);

namespace Yokai\Versioning\UpdateGuesser;

class ChainUpdateGuesser implements UpdateGuesserInterface
{
    /**
     * @var ChainableUpdateGuesserInterface[]|iterable
     */
    private $guessers;

    /**
     * @param ChainableUpdateGuesserInterface[]|iterable $guessers
     */
    public function __construct(iterable $guessers)
    {
        $this->guessers = $guessers;
    }

    /**
     * @inheritDoc
     */
    public function guessUpdates(object $entity, string $action): iterable
    {
        $updates = [];

        foreach ($this->guessers as $guesser) {
            if (!$guesser->supportAction($action)) {
                continue;
            }

            foreach ($guesser->guessUpdates($entity, $action) as $object) {
                $updates[spl_object_hash($object)] = $object;
            }
        }

        return array_values($updates);
    }
}
