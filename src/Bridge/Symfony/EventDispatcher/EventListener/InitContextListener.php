<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\EventDispatcher\EventListener;

use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Yokai\Versioning\Context;
use Yokai\Versioning\VersionableAuthorInterface;

class InitContextListener
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TokenStorageInterface|null
     */
    private $tokenStorage;

    public function __construct(Context $context, TokenStorageInterface $tokenStorage = null)
    {
        $this->context = $context;
        $this->tokenStorage = $tokenStorage;
    }

    public function onCommand(ConsoleEvent $event): void
    {
        $command = $event->getCommand();
        if (null === $command) {
            return;
        }

        $input = $event->getInput();

        $this->initialize(
            $command->getName(),
            array_merge($input->getArguments(), $input->getOptions())
        );
    }

    public function onRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        $this->initialize(
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params', [])
        );
    }

    private function initialize(?string $entryPoint, array $parameters): void
    {
        if ($entryPoint !== null) {
            ksort($parameters);
            $this->context->setEntryPoint($entryPoint);
            $this->context->setParameters($parameters);
        }

        if ($this->tokenStorage !== null
            && (null !== $token = $this->tokenStorage->getToken())
            && ($author = $token->getUser()) instanceof VersionableAuthorInterface
        ) {
            $this->context->setAuthor($author);
        }
    }
}
