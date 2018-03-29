<?php declare(strict_types=1);

namespace Yokai\Versioning\Initialize;

class ChainObjectFinder implements ObjectFinderInterface
{
    /**
     * @var ChainableObjectFinderInterface[]
     */
    private $objectFinders;

    /**
     * @param iterable|ChainableObjectFinderInterface[] $objectFinders
     */
    public function __construct(iterable $objectFinders)
    {
        $this->objectFinders = $objectFinders;
    }

    /**
     * @inheritdoc
     */
    public function find(string $class): iterable
    {
        foreach ($this->objectFinders as $objectFinder) {
            if ($objectFinder->supports($class)) {
                return $objectFinder->find($class);
            }
        }

        throw new \InvalidArgumentException(sprintf('Unsupported class "%s".', $class));
    }
}
