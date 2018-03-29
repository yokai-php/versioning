<?php declare(strict_types=1);

namespace Yokai\Versioning\Exception;

use InvalidArgumentException;

class InvalidResourceException extends InvalidArgumentException
{
    public static function unknownClass(string $class): self
    {
        return new self(
            sprintf('Unknown resource class "%s".', $class)
        );
    }

    public static function unknownType(string $type): self
    {
        return new self(
            sprintf('Unknown resource type "%s".', $type)
        );
    }
}
