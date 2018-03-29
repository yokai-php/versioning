<?php

namespace Yokai\Versioning\Tests\Unit\Finder;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Finder\AuthorFinder;
use Yokai\Versioning\Storage\AuthorStorageInterface;
use Yokai\Versioning\Tests\Acme\Domain\Admin;
use Yokai\Versioning\Tests\Acme\Domain\User;
use Yokai\Versioning\Tests\TypesConfigFactory;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\VersionableAuthorInterface;
use Yokai\Versioning\VersionInterface;

class AuthorFinderTest extends TestCase
{
    /**
     * @var TypesConfig
     */
    private static $typesConfig;

    /**
     * @var AuthorStorageInterface|ObjectProphecy
     */
    private $authorStorage;

    public static function setUpBeforeClass(): void
    {
        self::$typesConfig = TypesConfigFactory::get();
    }

    public static function tearDownAfterClass(): void
    {
        self::$typesConfig = null;
    }

    public function setUp(): void
    {
        $this->authorStorage = $this->prophesize(AuthorStorageInterface::class);
    }

    public function tearDown(): void
    {
        $this->authorStorage = null;
    }

    /**
     * @test
     * @dataProvider versionProvider
     */
    public function it_find_author_for_version(VersionInterface $version, ?VersionableAuthorInterface $author): void
    {
        if ($author !== null) {
            $this->authorStorage->get(get_class($author), $author->getVersionableId())
                ->shouldBeCalledTimes(1)
                ->willReturn($author);
        } else {
            $this->authorStorage->get(Argument::any(), Argument::any())
                ->shouldNotBeCalled();
        }

        $finder = new AuthorFinder(self::$typesConfig, $this->authorStorage->reveal());

        self::assertSame($author, $finder->findForVersion($version));
    }

    public function versionProvider(): \Generator
    {
        $user = new User('1');
        $admin = new Admin('1');

        /** @var VersionInterface|ObjectProphecy $userVersion */
        $userVersion = $this->prophesize(VersionInterface::class);
        $userVersion->getAuthorType()->willReturn(User::VERSION_TYPE);
        $userVersion->getAuthorId()->willReturn('1');

        /** @var VersionInterface|ObjectProphecy $adminVersion */
        $adminVersion = $this->prophesize(VersionInterface::class);
        $adminVersion->getAuthorType()->willReturn(Admin::VERSION_TYPE);
        $adminVersion->getAuthorId()->willReturn('1');

        /** @var VersionInterface|ObjectProphecy $noAuthorTypeVersion */
        $noAuthorTypeVersion = $this->prophesize(VersionInterface::class);
        $noAuthorTypeVersion->getAuthorType()->willReturn(null);
        $noAuthorTypeVersion->getAuthorId()->willReturn('42');

        /** @var VersionInterface|ObjectProphecy $noAuthorIdVersion */
        $noAuthorIdVersion = $this->prophesize(VersionInterface::class);
        $noAuthorIdVersion->getAuthorType()->willReturn('foo');
        $noAuthorIdVersion->getAuthorId()->willReturn(null);

        /** @var VersionInterface|ObjectProphecy $noAuthorTypeNorIdVersion */
        $noAuthorTypeNorIdVersion = $this->prophesize(VersionInterface::class);
        $noAuthorTypeNorIdVersion->getAuthorType()->willReturn(null);
        $noAuthorTypeNorIdVersion->getAuthorId()->willReturn(null);

        yield [$userVersion->reveal(), $user];
        yield [$adminVersion->reveal(), $admin];
        yield [$noAuthorTypeVersion->reveal(), null];
        yield [$noAuthorIdVersion->reveal(), null];
        yield [$noAuthorTypeNorIdVersion->reveal(), null];
    }
}
