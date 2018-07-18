<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Yokai\Versioning\SnapshotTakerInterface;
use Yokai\Versioning\VersionableResourceInterface;

class NormalizerSnapshotTaker implements SnapshotTakerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @inheritdoc
     */
    public function take(VersionableResourceInterface $resource): array
    {
        $snapshot = $this->normalizer->normalize($resource);
        ksort($snapshot);

        return $snapshot;
    }
}
