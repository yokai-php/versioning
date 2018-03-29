<?php

namespace Yokai\Versioning\Tests\Unit\Purge;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Purge\ChainPurger;
use Yokai\Versioning\Purge\PurgerInterface;

class ChainPurgerTest extends TestCase
{
    /**
     * @test
     */
    public function it_purger_delegating_to_other_purgers(): void
    {
        /** @var PurgerInterface|ObjectProphecy $purger1 */
        $purger1 = $this->prophesize(PurgerInterface::class);
        $purger1->purge()->shouldBeCalledTimes(1)->willReturn(10);

        /** @var PurgerInterface|ObjectProphecy $purger2 */
        $purger2 = $this->prophesize(PurgerInterface::class);
        $purger2->purge()->shouldBeCalledTimes(1)->willReturn(20);

        $purger = new ChainPurger([$purger1->reveal(), $purger2->reveal()]);

        self::assertSame(30, $purger->purge());
    }
}
