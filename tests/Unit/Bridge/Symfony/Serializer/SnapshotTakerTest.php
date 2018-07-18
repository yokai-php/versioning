<?php

namespace Yokai\Versioning\Tests\Unit\Bridge\Symfony\Serializer;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Yokai\Versioning\Bridge\Symfony\Serializer\NormalizerSnapshotTaker;
use Yokai\Versioning\Tests\Acme\Domain\Product;

class SnapshotTakerTest extends TestCase
{
    /**
     * @var NormalizerInterface|ObjectProphecy
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = $this->prophesize(NormalizerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->normalizer = null;
    }

    /**
     * @test
     */
    public function it_normalize_object_to_take_a_snapshot(): void
    {
        $product = new Product('1');

        $this->normalizer->normalize($product)
            ->shouldBeCalledTimes(1)
            ->willReturn(['name' => 'Spoon', 'price' => 0.99, 'id' => 1]);

        $snapshotTaker = new NormalizerSnapshotTaker($this->normalizer->reveal());

        self::assertSame(
            ['id' => 1, 'name' => 'Spoon', 'price' => 0.99],
            $snapshotTaker->take($product)
        );
    }
}
