<?php declare(strict_types=1);

namespace Yokai\Versioning;

use DateTime;
use DateTimeImmutable;

/**
 * A very simple time machine.
 * Don't be scarred, this is all about testing.
 */
final class Time
{
    /**
     * @var string|null
     */
    private static $now;

    public static function mock(string $now): void
    {
        self::$now = $now;
    }

    public static function unmock(): void
    {
        self::$now = null;
    }

    public static function mutable(): DateTime
    {
        return new DateTime(self::$now ?: 'now');
    }

    public static function immutable(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::$now ?: 'now');
    }
}
