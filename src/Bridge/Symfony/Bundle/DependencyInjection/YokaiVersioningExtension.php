<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Bundle\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Serializer;
use Yokai\Versioning\Initialize\ChainableObjectFinderInterface;
use Yokai\Versioning\Purge\ChainablePurgerInterface;
use Yokai\Versioning\Storage\ChainableAuthorStorageInterface;
use Yokai\Versioning\UpdateGuesser\ChainableUpdateGuesserInterface;

class YokaiVersioningExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));

        $container->registerForAutoconfiguration(ChainableObjectFinderInterface::class)
            ->addTag('yokai.versioning.initialize.object_finder');
        $container->registerForAutoconfiguration(ChainablePurgerInterface::class)
            ->addTag('yokai.versioning.purger');
        $container->registerForAutoconfiguration(ChainableAuthorStorageInterface::class)
            ->addTag('yokai.versioning.author_storage');
        $container->registerForAutoconfiguration(ChainableUpdateGuesserInterface::class)
            ->addTag('yokai.versioning.update_guesser');

        $loader->load('services.xml');

        if (class_exists(Application::class)) {
            $loader->load('console.xml');
        }

        if (class_exists(EntityManagerInterface::class)) {
            $loader->load('doctrine-orm.xml');
        }

        if (class_exists(EventDispatcherInterface::class)) {
            $loader->load('event-dispatcher.xml');
            $listener = $container->getDefinition('yokai.versioning.event_listener.init_context_listener');

            if (class_exists(KernelEvents::class)) {
                $listener->addTag('kernel.event_listener', ['name' => KernelEvents::REQUEST, 'method' => 'onRequest']);
            }
            if (class_exists(Application::class)) {
                $listener->addTag('kernel.event_listener', ['name' => ConsoleEvents::COMMAND, 'method' => 'onCommand']);
            }
        }

        if (class_exists(Serializer::class)) {
            $loader->load('serializer.xml');

            if (class_exists(EntityManagerInterface::class)) {
                $loader->load('serializer.doctrine-orm.xml');
            }
        }

        $loader->load('aliases.xml');
    }
}
