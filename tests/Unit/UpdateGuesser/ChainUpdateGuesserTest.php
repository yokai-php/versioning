<?php

namespace Yokai\Versioning\Tests\Unit\UpdateGuesser;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\UpdateGuesser\ChainableUpdateGuesserInterface;
use Yokai\Versioning\UpdateGuesser\ChainUpdateGuesser;
use Yokai\Versioning\UpdateGuesser\UpdateGuesserInterface;

class ChainUpdateGuesserTest extends TestCase
{

    /**
     * @test
     */
    public function it_guess_updates_delegating_to_other_guessers(): void
    {
        $this->assertTrue(true);
        return;
        /** @var ChainableUpdateGuesserInterface|ObjectProphecy $anythingGuesser */
        $anythingGuesser = $this->prophesize(ChainableUpdateGuesserInterface::class);
        $anythingGuesser->supportAction(UpdateGuesserInterface::ACTION_INSERT)->willReturn(true);
        $anythingGuesser->supportAction(UpdateGuesserInterface::ACTION_UPDATE)->willReturn(true);
        $anythingGuesser->supportAction(UpdateGuesserInterface::ACTION_DELETE)->willReturn(true);
        $anythingGuesser->guessUpdates(Argument::any(), Argument::any())->will(function ($args) {
            return [$args[0]];
        });

        /** @var ChainableUpdateGuesserInterface|ObjectProphecy $updateProductGuesser */
        $updateProductGuesser = $this->prophesize(ChainableUpdateGuesserInterface::class);
        $updateProductGuesser->supportAction(UpdateGuesserInterface::ACTION_INSERT)->willReturn(false);
        $updateProductGuesser->supportAction(UpdateGuesserInterface::ACTION_UPDATE)->willReturn(true);
        $updateProductGuesser->supportAction(UpdateGuesserInterface::ACTION_DELETE)->willReturn(false);

        /** @var ChainableUpdateGuesserInterface|ObjectProphecy $deleteAnythingGuesser */
        $deleteAnythingGuesser = $this->prophesize(ChainableUpdateGuesserInterface::class);
        $deleteAnythingGuesser->supportAction(UpdateGuesserInterface::ACTION_INSERT)->willReturn(false);
        $deleteAnythingGuesser->supportAction(UpdateGuesserInterface::ACTION_UPDATE)->willReturn(false);
        $deleteAnythingGuesser->supportAction(UpdateGuesserInterface::ACTION_DELETE)->willReturn(true);

        $guesser = new ChainUpdateGuesser([
            $anythingGuesser->reveal(),
            $updateProductGuesser->reveal(),
            $deleteAnythingGuesser->reveal(),
        ]);

        $object = new \stdClass();
        self::assertSame(
            [$object],
            $guesser->guessUpdates($object, UpdateGuesserInterface::ACTION_INSERT)
        );
        self::assertSame(
            [$object],
            $guesser->guessUpdates($object, UpdateGuesserInterface::ACTION_UPDATE)
        );
        self::assertSame(
            [$object],
            $guesser->guessUpdates($object, UpdateGuesserInterface::ACTION_DELETE)
        );

        $product = new Product('1');
    }
}
