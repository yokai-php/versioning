<?php declare(strict_types=1);

namespace Yokai\Versioning;

class Context
{
    /**
     * @var VersionableAuthorInterface|null
     */
    private $author;

    /**
     * @var string|null
     */
    private $entryPoint;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @return null|VersionableAuthorInterface
     */
    public function getAuthor(): ?VersionableAuthorInterface
    {
        return $this->author;
    }

    /**
     * @param VersionableAuthorInterface $author
     */
    public function setAuthor(VersionableAuthorInterface $author): void
    {
        $this->author = $author;
    }

    /**
     * @return null|string
     */
    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }

    /**
     * @param string $entryPoint
     */
    public function setEntryPoint(string $entryPoint): void
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
