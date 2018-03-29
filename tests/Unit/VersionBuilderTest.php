<?php

namespace Yokai\Versioning\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\ChangesetBuilder;
use Yokai\Versioning\Context;
use Yokai\Versioning\SnapshotTakerInterface;
use Yokai\Versioning\Storage\VersionStorageInterface;
use Yokai\Versioning\Tests\Acme\Domain\Product;
use Yokai\Versioning\Tests\Acme\Domain\User;
use Yokai\Versioning\Tests\TypesConfigFactory;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionBuilder;
use Yokai\Versioning\VersionFactoryInterface;
use Yokai\Versioning\VersionInterface;

class VersionBuilderTest extends TestCase
{
    /**
     * @var TypesConfig
     */
    private static $typesConfig;

    /**
     * @var ChangesetBuilder
     */
    private static $changesetBuilder;

    /**
     * @var VersionStorageInterface|ObjectProphecy
     */
    private $versionStorage;

    /**
     * @var SnapshotTakerInterface|ObjectProphecy
     */
    private $snapshotBuilder;

    /**
     * @var VersionFactoryInterface|ObjectProphecy
     */
    private $versionFactory;

    /**
     * @var Context
     */
    private $context;

    public static function setUpBeforeClass(): void
    {
        self::$typesConfig = TypesConfigFactory::get();
        self::$changesetBuilder = new ChangesetBuilder();
    }

    public static function tearDownAfterClass(): void
    {
        self::$typesConfig = self::$changesetBuilder = null;
    }

    protected function setUp(): void
    {
        $this->versionStorage = $this->prophesize(VersionStorageInterface::class);
        $this->snapshotBuilder = $this->prophesize(SnapshotTakerInterface::class);
        $this->versionFactory = $this->prophesize(VersionFactoryInterface::class);
        $this->context = new Context();
    }

    protected function tearDown(): void
    {
        $this->versionStorage = $this->snapshotBuilder = $this->versionFactory = $this->context = null;
    }

    /**
     * @test
     * @dataProvider contextProvider
     */
    public function it_build_first_version_of_resource(?string $entryPoint, array $parameters, ?User $author): void
    {
        $versionBuilder = new VersionBuilder(
            self::$typesConfig,
            $this->versionStorage->reveal(),
            $this->snapshotBuilder->reveal(),
            self::$changesetBuilder,
            $this->context,
            $this->versionFactory->reveal()
        );

        if ($entryPoint !== null) {
            $this->context->setEntryPoint($entryPoint);
        }
        if (!empty($parameters)) {
            $this->context->setParameters($parameters);
        }
        if ($author !== null) {
            $this->context->setAuthor($author);
        }

        $normalizedAuthor = [null, null];
        if ($author !== null) {
            $normalizedAuthor = [$author::VERSION_TYPE, $author->id];
        }

        $resource = new Product('1');

        $this->versionStorage->currentForResource('product', '1')
            ->shouldBeCalledTimes(1)
            ->willReturn(null);
        $this->snapshotBuilder->take($resource)
            ->shouldBeCalledTimes(1)
            ->willReturn(['id' => '1', 'name' => 'Spoon']);

        $version = $this->prophesize(VersionInterface::class)->reveal();
        $this->versionFactory->create(
            ['product', '1'],
            1,
            ['id' => '1', 'name' => 'Spoon'],
            ['id' => ['old' => '', 'new' => '1'], 'name' => ['old' => '', 'new' => 'Spoon']],
            $normalizedAuthor,
            [$entryPoint, $parameters],
            Argument::type(\DateTimeImmutable::class)
        )
            ->shouldBeCalledTimes(1)
            ->willReturn($version);

        self::assertSame(
            $version,
            $versionBuilder->build($resource)
        );
    }

    /**
     * @test
     * @dataProvider contextProvider
     */
    public function it_build_new_version_of_resource(?string $entryPoint, array $parameters, ?User $author): void
    {
        $versionBuilder = new VersionBuilder(
            self::$typesConfig,
            $this->versionStorage->reveal(),
            $this->snapshotBuilder->reveal(),
            self::$changesetBuilder,
            $this->context,
            $this->versionFactory->reveal()
        );

        if ($entryPoint !== null) {
            $this->context->setEntryPoint($entryPoint);
        }
        if (!empty($parameters)) {
            $this->context->setParameters($parameters);
        }
        if ($author !== null) {
            $this->context->setAuthor($author);
        }

        $normalizedAuthor = [null, null];
        if ($author !== null) {
            $normalizedAuthor = [$author::VERSION_TYPE, $author->id];
        }

        $resource = new Product('1');

        /** @var VersionInterface|ObjectProphecy $oldVersion */
        $oldVersion = $this->prophesize(VersionInterface::class);
        $oldVersion->getVersion()->willReturn(1);
        $oldVersion->getSnapshot()->willReturn(['id' => '1', 'name' => 'Spoon']);

        $this->versionStorage->currentForResource('product', '1')
            ->shouldBeCalledTimes(1)
            ->willReturn($oldVersion->reveal());
        $this->snapshotBuilder->take($resource)
            ->shouldBeCalledTimes(1)
            ->willReturn(['id' => '1', 'name' => 'Spoon with fancy design']);

        $version = $this->prophesize(VersionInterface::class)->reveal();
        $this->versionFactory->create(
            ['product', '1'],
            2,
            ['id' => '1', 'name' => 'Spoon with fancy design'],
            ['name' => ['old' => 'Spoon', 'new' => 'Spoon with fancy design']],
            $normalizedAuthor,
            [$entryPoint, $parameters],
            Argument::type(\DateTimeImmutable::class)
        )
            ->shouldBeCalledTimes(1)
            ->willReturn($version);

        self::assertSame(
            $version,
            $versionBuilder->build($resource)
        );
    }

    public function contextProvider(): \Generator
    {
        yield [null, [], null];
        yield ['product_import', ['full' => true], null];
        yield [null, [], new User('1')];
        yield ['product_import', ['full' => true], new User('1')];
    }
}
