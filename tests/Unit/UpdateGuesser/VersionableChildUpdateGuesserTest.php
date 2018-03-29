<?php

namespace Yokai\Versioning\Tests\Unit\UpdateGuesser;

use PHPUnit\Framework\TestCase;
use Yokai\Versioning\Tests\Acme\Domain\Order;
use Yokai\Versioning\Tests\Acme\Domain\OrderItem;
use Yokai\Versioning\UpdateGuesser\VersionableChildUpdateGuesser;

class VersionableChildUpdateGuesserTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_all_actions(): void
    {
        $guesser = new VersionableChildUpdateGuesser();

        self::assertTrue($guesser->supportAction($guesser::ACTION_INSERT));
        self::assertTrue($guesser->supportAction($guesser::ACTION_UPDATE));
        self::assertTrue($guesser->supportAction($guesser::ACTION_DELETE));
    }

    /**
     * @test
     */
    public function it_guess_updates_for_resource_parent_objects(): void
    {
        $guesser = new VersionableChildUpdateGuesser();

        $actions = [$guesser::ACTION_INSERT, $guesser::ACTION_UPDATE, $guesser::ACTION_DELETE];

        $invalidObject = new \stdClass();
        $order = new Order('1', [$orderItem = new OrderItem('1')]);

        foreach ($actions as $action) {
            self::assertSame([], $guesser->guessUpdates($invalidObject, $action));
            self::assertSame([], $guesser->guessUpdates($order, $action));
            self::assertSame([$order], $guesser->guessUpdates($orderItem, $action));
        }
    }
}
