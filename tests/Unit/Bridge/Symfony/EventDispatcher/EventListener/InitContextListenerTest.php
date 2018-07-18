<?php

namespace Yokai\Versioning\Tests\Unit\Bridge\Symfony\EventDispatcher\EventListener;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\Versioning\Bridge\Symfony\EventDispatcher\EventListener\InitContextListener;
use Yokai\Versioning\Context;
use Yokai\Versioning\VersionableAuthorInterface;

class InitContextListenerTest extends TestCase
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TokenStorageInterface|ObjectProphecy
     */
    private $tokenStorage;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);
    }

    protected function tearDown(): void
    {
        $this->context = $this->tokenStorage = null;
    }

    /**
     * @test
     * @dataProvider toggleSecurityProvider
     */
    public function it_cannot_initialize_without_command(bool $security): void
    {
        $listener = new InitContextListener($this->context, $security ? $this->tokenStorage->reveal() : null);

        $listener->onCommand(
            new ConsoleEvent(
                null,
                $this->prophesize(InputInterface::class)->reveal(),
                $this->prophesize(OutputInterface::class)->reveal()
            )
        );

        self::assertNull($this->context->getEntryPoint());
        self::assertSame([], $this->context->getParameters());
        self::assertNull($this->context->getAuthor());
    }

    /**
     * @test
     * @dataProvider tokenProvider
     */
    public function it_initialize_with_command($token, VersionableAuthorInterface $author = null): void
    {
        if ($token !== false) {
            $this->tokenStorage->getToken()->willReturn($token);
        }

        $listener = new InitContextListener($this->context, $token !== false ? $this->tokenStorage->reveal() : null);

        /** @var InputInterface|ObjectProphecy $input */
        $input = $this->prophesize(InputInterface::class);
        $input->getOptions()->shouldBeCalledTimes(1)->willReturn(['force' => true]);
        $input->getArguments()->shouldBeCalledTimes(1)->willReturn(['type' => 'daily']);

        $listener->onCommand(
            new ConsoleEvent(
                new Command('product:import'),
                $input->reveal(),
                $this->prophesize(OutputInterface::class)->reveal()
            )
        );

        self::assertSame('product:import', $this->context->getEntryPoint());
        self::assertSame(['force' => true, 'type' => 'daily'], $this->context->getParameters());
        self::assertSame($author, $this->context->getAuthor());
    }

    /**
     * @test
     * @dataProvider toggleSecurityProvider
     */
    public function it_cannot_initialize_without_route(bool $security): void
    {
        $listener = new InitContextListener($this->context, $security ? $this->tokenStorage->reveal() : null);

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $request = new Request();
        $listener->onRequest(
            new GetResponseEvent($kernel->reveal(), $request, HttpKernelInterface::MASTER_REQUEST)
        );

        self::assertNull($this->context->getEntryPoint());
        self::assertSame([], $this->context->getParameters());
        self::assertNull($this->context->getAuthor());
    }

    /**
     * @test
     * @dataProvider tokenProvider
     */
    public function it_initialize_with_route($token, VersionableAuthorInterface $author = null): void
    {
        if ($token !== false) {
            $this->tokenStorage->getToken()->willReturn($token);
        }

        $listener = new InitContextListener($this->context, $token !== false ? $this->tokenStorage->reveal() : null);

        $kernel = $this->prophesize(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', 'route_to_update_product');
        $request->attributes->set('_route_params', ['id' => 1, 'context' => 'admin']);
        $listener->onRequest(
            new GetResponseEvent($kernel->reveal(), $request, HttpKernelInterface::MASTER_REQUEST)
        );

        self::assertSame('route_to_update_product', $this->context->getEntryPoint());
        self::assertSame(['context' => 'admin', 'id' => 1], $this->context->getParameters());
        self::assertSame($author, $this->context->getAuthor());
    }

    public function toggleSecurityProvider(): \Generator
    {
        yield [true];
        yield [false];
    }

    public function tokenProvider(): \Generator
    {
        yield 'no security' => [false];
        yield 'no token' => [null];

        /** @var TokenInterface|ObjectProphecy $tokenWithoutUser */
        $tokenWithoutUser = $this->prophesize(TokenInterface::class);

        yield 'token but no user' => [$tokenWithoutUser->reveal()];

        $user = $this->prophesize(UserInterface::class)->reveal();
        /** @var TokenInterface|ObjectProphecy $tokenWithoutAuthor */
        $tokenWithoutAuthor = $this->prophesize(TokenInterface::class);
        $tokenWithoutAuthor->getUser()->shouldBeCalledTimes(1)->willReturn($user);

        yield 'token with not author user' => [$tokenWithoutAuthor->reveal()];

        $author = $this->prophesize(VersionableAuthorInterface::class)->reveal();
        /** @var TokenInterface|ObjectProphecy $tokenWithAuthor */
        $tokenWithAuthor = $this->prophesize(TokenInterface::class);
        $tokenWithAuthor->getUser()->shouldBeCalledTimes(1)->willReturn($author);

        yield 'token with author user' => [$tokenWithAuthor->reveal(), $author];
    }
}
