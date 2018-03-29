<?php declare(strict_types=1);

namespace Yokai\Versioning\Exception;

use InvalidArgumentException;

class InvalidAuthorException extends InvalidArgumentException
{
    public static function unknownClass(string $class): self
    {
        return new self(
            sprintf('Unknown author class "%s".', $class)
        );
    }

    public static function unknownType(string $type): self
    {
        return new self(
            sprintf('Unknown author type "%s".', $type)
        );
    }
}
