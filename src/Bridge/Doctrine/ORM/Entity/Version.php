<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Doctrine\ORM\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Yokai\Versioning\VersionInterface;

class Version implements VersionInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $resourceType;

    /**
     * @var string
     */
    private $resourceId;

    /**
     * @var int
     */
    private $version;

    /**
     * @var array
     */
    private $snapshot;

    /**
     * @var array
     */
    private $changeset;

    /**
     * @var string|null
     */
    private $authorType;

    /**
     * @var string|null
     */
    private $authorId;

    /**
     * @var string|null
     */
    private $contextEntryPoint;

    /**
     * @var array
     */
    private $contextParameters;

    /**
     * @var DateTimeImmutable
     */
    private $loggedAt;

    public function __construct(
        string $resourceType,
        string $resourceId,
        int $version,
        array $snapshot,
        array $changeSet,
        ?string $authorType,
        ?string $authorId,
        ?string $contextEntryPoint,
        array $contextParameters,
        DateTimeImmutable $loggedAt
    ) {
        $this->resourceType = $resourceType;
        $this->resourceId = $resourceId;
        $this->version = $version;
        $this->snapshot = $snapshot;
        $this->changeset = $changeSet;
        $this->authorType = $authorType;
        $this->authorId = $authorId;
        $this->contextEntryPoint = $contextEntryPoint;
        $this->contextParameters = $contextParameters;
        $this->loggedAt = $loggedAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getSnapshot(): array
    {
        return $this->snapshot;
    }

    /**
     * @return array
     */
    public function getChangeset(): array
    {
        return $this->changeset;
    }

    /**
     * @return string|null
     */
    public function getAuthorType(): ?string
    {
        return $this->authorType;
    }

    /**
     * @return string|null
     */
    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLoggedAt(): DateTimeInterface
    {
        return $this->loggedAt;
    }

    /**
     * @return null|string
     */
    public function getContextEntryPoint(): ?string
    {
        return $this->contextEntryPoint;
    }

    /**
     * @return array
     */
    public function getContextParameters(): array
    {
        return $this->contextParameters;
    }
}
