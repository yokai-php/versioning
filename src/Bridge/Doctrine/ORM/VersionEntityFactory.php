<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM;

use DateTimeImmutable;
use DateTimeInterface;
use Yokai\Versioning\Bridge\Doctrine\ORM\Entity\Version;
use Yokai\Versioning\VersionFactoryInterface;
use Yokai\Versioning\VersionInterface;

class VersionEntityFactory implements VersionFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(
        array $resource,
        int $version,
        array $snapshot,
        array $changeSet,
        array $author,
        array $context,
        DateTimeInterface $loggedAt
    ): VersionInterface {
        if (!$loggedAt instanceof DateTimeImmutable) {
            $loggedAt = DateTimeImmutable::createFromFormat(DATE_ISO8601, $loggedAt->format(DATE_ISO8601));
        }

        return new Version(
            $resource[0],
            $resource[1],
            $version,
            $snapshot,
            $changeSet,
            $author[0],
            $author[1],
            $context[0],
            $context[1],
            $loggedAt
        );
    }
}
