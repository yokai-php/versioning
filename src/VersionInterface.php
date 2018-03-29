<?php declare(strict_types=1);

namespace Yokai\Versioning;

use DateTimeInterface;

interface VersionInterface
{
    /**
     * @return string
     */
    public function getResourceType(): string;

    /**
     * @return string
     */
    public function getResourceId(): string;

    /**
     * @return int
     */
    public function getVersion(): int;

    /**
     * @return array
     */
    public function getSnapshot(): array;

    /**
     * @return array
     */
    public function getChangeset(): array;

    /**
     * @return string|null
     */
    public function getAuthorType(): ?string;

    /**
     * @return string|null
     */
    public function getAuthorId(): ?string;

    /**
     * @return DateTimeInterface
     */
    public function getLoggedAt(): DateTimeInterface;

    /**
     * @return null|string
     */
    public function getContextEntryPoint(): ?string;

    /**
     * @return array
     */
    public function getContextParameters(): array;
}
