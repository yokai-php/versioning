<?php

namespace Yokai\Versioning\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yokai\Versioning\ChangesetBuilder;

class ChangesetBuilderTest extends TestCase
{
    /**
     * @var ChangesetBuilder
     */
    private static $changesetBuilder;

    public static function setUpBeforeClass(): void
    {
        self::$changesetBuilder = new ChangesetBuilder();
    }

    public static function tearDownAfterClass(): void
    {
        self::$changesetBuilder = null;
    }

    /**
     * @test
     * @dataProvider changesetProvider
     */
    public function it_build_changeset(array $oldSnapshot, array $newSnapshot, array $expectedChangeset): void
    {
        self::assertSame(
            $expectedChangeset,
            self::$changesetBuilder->build($oldSnapshot, $newSnapshot)
        );
    }

    public function changesetProvider(): \Generator
    {
        // init
        yield [
            [],
            [
                'id' => 1,
                'shipTo' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin'],
                'totalPrice' => 100.00,
                'items' => [1, 2, 3, 4],
                'seller' => null,
            ],
            [
                'id' => ['old' => '', 'new' => 1],
                'shipTo' => ['old' => '', 'new' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin']],
                'totalPrice' => ['old' => '', 'new' => 100.00],
                'items' => ['old' => '', 'new' => [1, 2, 3, 4]],
            ],
        ];
        // ordered new items
        yield [
            [
                'id' => 1,
                'shipTo' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin'],
                'totalPrice' => 100.00,
                'items' => [1, 2, 3, 4],
            ],
            [
                'id' => 1,
                'shipTo' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin'],
                'totalPrice' => 100,
                'items' => [1, 2, 4, 5],
            ],
            [
                'items' => ['old' => [1, 2, 3, 4], 'new' => [1, 2, 4, 5]],
            ],
        ];
        // change shipping address
        yield [
            [
                'id' => 1,
                'shipTo' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin'],
                'totalPrice' => 100,
                'items' => [1, 2, 4, 5],
            ],
            [
                'id' => 1,
                'shipTo' => ['person' => 'John Doe', 'address' => '9971 Longfellow St', 'city' => 'Champlin'],
                'totalPrice' => 100,
                'items' => [1, 2, 4, 5],
            ],
            [
                'shipTo' => ['old' => ['person' => 'John Doe', 'address' => '214 Circle Road', 'city' => 'Champlin'], 'new' => ['person' => 'John Doe', 'address' => '9971 Longfellow St', 'city' => 'Champlin']],
            ],
        ];
    }
}
