<?php

namespace Yokai\Versioning\Tests\Unit\UpdateGuesser;

use PHPUnit\Framework\TestCase;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\UpdateGuesser\VersionableUpdateGuesser;

class VersionableUpdateGuesserTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_certain_actions(): void
    {
        $guesser = new VersionableUpdateGuesser();

        self::assertTrue($guesser->supportAction($guesser::ACTION_INSERT));
        self::assertTrue($guesser->supportAction($guesser::ACTION_UPDATE));
        self::assertFalse($guesser->supportAction($guesser::ACTION_DELETE));
    }

    /**
     * @test
     */
    public function it_guess_updates_for_resources(): void
    {
        $guesser = new VersionableUpdateGuesser();

        $actions = [$guesser::ACTION_INSERT, $guesser::ACTION_UPDATE];

        $invalidObject = new \stdClass();
        $orderItem = new OrderItem('1');
        $product = new Product('1');

        foreach ($actions as $action) {
            self::assertSame([], $guesser->guessUpdates($invalidObject, $action));
            self::assertSame([$orderItem], $guesser->guessUpdates($orderItem, $action));
            self::assertSame([$product], $guesser->guessUpdates($product, $action));
        }
    }
}
