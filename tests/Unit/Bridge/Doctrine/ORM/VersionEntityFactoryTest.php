<?php

namespace Yokai\Versioning\Tests\Unit\Bridge\Doctrine\ORM;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Yokai\Versioning\Bridge\Doctrine\ORM\Entity\Version;
use Yokai\Versioning\Bridge\Doctrine\ORM\VersionEntityFactory;
use Yokai\Versioning\Tests\Acme\Domain\Admin;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Time;

class VersionEntityFactoryTest extends TestCase
{
    private const TIME = '2018-05-04 12:00:00';

    public static function setUpBeforeClass(): void
    {
        Time::mock(self::TIME);
    }

    public static function tearDownAfterClass(): void
    {
        Time::unmock();
    }

    /**
     * @test
     */
    public function it_create_version_entity(): void
    {
        $version = (new VersionEntityFactory())->create(
            [Product::VERSION_TYPE, '1'],
            1,
            ['id' => 1, 'name' => 'Spoon'],
            ['id' => ['old' => '', 'new' => 1], 'name' => ['old' => '', 'new' => 'Spoon']],
            [Admin::VERSION_TYPE, '1'],
            ['route_to_update_product', ['id' => 1, 'context' => 'admin']],
            Time::mutable()
        );

        self::assertInstanceOf(Version::class, $version);
        /** @var $version Version */

        self::assertNull($version->getId());
        self::assertSame(Product::VERSION_TYPE, $version->getResourceType());
        self::assertSame('1', $version->getResourceId());
        self::assertSame(1, $version->getVersion());
        self::assertSame(['id' => 1, 'name' => 'Spoon'], $version->getSnapshot());
        self::assertSame(['id' => ['old' => '', 'new' => 1], 'name' => ['old' => '', 'new' => 'Spoon']], $version->getChangeSet());
        self::assertSame(Admin::VERSION_TYPE, $version->getAuthorType());
        self::assertSame('1', $version->getAuthorId());
        self::assertInstanceOf(DateTimeImmutable::class, $version->getLoggedAt());
        self::assertSame(self::TIME, $version->getLoggedAt()->format('Y-m-d H:i:s'));
        self::assertSame('route_to_update_product', $version->getContextEntryPoint());
        self::assertSame(['id' => 1, 'context' => 'admin'], $version->getContextParameters());
    }
}
