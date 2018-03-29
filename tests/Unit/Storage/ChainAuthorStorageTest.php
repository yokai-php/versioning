<?php

namespace Yokai\Versioning\Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\Versioning\Exception\InvalidAuthorException;
use Yokai\Versioning\Storage\ChainableAuthorStorageInterface;
use Yokai\Versioning\Storage\ChainAuthorStorage;
use Yokai\Versioning\Tests\Acme\Domain\Admin;
use Yokai\Versioning\Tests\Acme\Domain\User;

class ChainAuthorStorageTest extends TestCase
{
    /**
     * @test
     */
    public function it_get_author_delegating_to_other_storages(): void
    {
        /** @var ChainableAuthorStorageInterface|ObjectProphecy $userStorage */
        $userStorage = $this->prophesize(ChainableAuthorStorageInterface::class);
        $userStorage->supports(Argument::any())->will(function (array $args) { return $args[0] === User::class; });
        $userStorage->get(User::class, Argument::any())->will(function (array $args) { return new User($args[1]); });

        /** @var ChainableAuthorStorageInterface|ObjectProphecy $adminStorage */
        $adminStorage = $this->prophesize(ChainableAuthorStorageInterface::class);
        $adminStorage->supports(Argument::any())->will(function (array $args) { return $args[0] === Admin::class; });
        $adminStorage->get(Admin::class, Argument::any())->will(function (array $args) { return new Admin($args[1]); });

        $storage = new ChainAuthorStorage([$userStorage->reveal(), $adminStorage->reveal()]);

        /** @var User $user1 */
        $user1 = $storage->get(User::class, '1');
        self::assertInstanceOf(User::class, $user1);
        self::assertSame('1', $user1->id);

        /** @var User $user2 */
        $user2 = $storage->get(User::class, '2');
        self::assertInstanceOf(User::class, $user2);
        self::assertSame('2', $user2->id);

        /** @var Admin $admin1 */
        $admin1 = $storage->get(Admin::class, '1');
        self::assertInstanceOf(Admin::class, $admin1);
        self::assertSame('1', $admin1->id);

        /** @var Admin $admin2 */
        $admin2 = $storage->get(Admin::class, '2');
        self::assertInstanceOf(Admin::class, $admin2);
        self::assertSame('2', $admin2->id);

        $exceptionThrown = null;
        try {
            $storage->get('Class\\That\\Do\\Not\\Exist', '1');
        } catch (InvalidAuthorException $e) {
            $exceptionThrown = $e;
        }

        self::assertInstanceOf(InvalidAuthorException::class, $exceptionThrown);
        self::assertSame('Unknown author class "Class\\That\\Do\\Not\\Exist".', $exceptionThrown->getMessage());
    }
}
