<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('yokai_versioning');

        $rootNode
            ->info('')
            ->children()
            ->end()
        ;

        $this->addTypesSection($rootNode);
        $this->addStorageSection($rootNode);
        $this->addSnapshotSection($rootNode);

        return $treeBuilder;
    }

    private function addTypesSection(ArrayNodeDefinition $rootNode): void
    {
        $notUniqueValues = function ($value) {
            return count($value) === count(array_unique($value));
        };

        $rootNode
            ->children()
                ->arrayNode('types')
                    ->info('')
                    ->children()
                        ->arrayNode('resources')
                            ->validate()
                                ->ifTrue($notUniqueValues)
                                ->thenInvalid('')
                            ->end()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->info('')->end()
                        ->end()
                        ->arrayNode('authors')
                            ->validate()
                                ->ifTrue($notUniqueValues)
                                ->thenInvalid('')
                            ->end()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->info('')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addStorageSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('storage')
                    ->info('')
                    ->children()
                        ->scalarNode('version')
                            ->defaultValue('')
                            ->info('')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addSnapshotSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('snapshot')
                    ->info('')
                    ->children()
                        ->scalarNode('taker')
                            ->defaultValue('')
                            ->info('')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
