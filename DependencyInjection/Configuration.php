<?php

declare(strict_types=1);

/*
 * This file is part of the App Insights PHP project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppInsightsPHP\Symfony\AppInsightsPHPBundle\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('app_insights_php');
        $rootNode = \method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('app_insights_php');

        $allowedLoggerTypes = ['trace', 'dependency'];

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->booleanNode('gzip_enabled')->defaultFalse()->end()
                ->scalarNode('instrumentation_key')->isRequired()->end()
                ->arrayNode('fallback_logger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('service_id')->end()
                        ->scalarNode('monolog_channel')->end()
                    ->end()
                ->end()
                ->scalarNode('failure_cache_service_id')->defaultNull()->end()
                ->arrayNode('exceptions')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->arrayNode('ignored_exceptions')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('dependencies')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('traces')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('requests')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('track_dependency')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('monolog')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('handlers')
                            ->canBeUnset()
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('level')->defaultValue(Logger::DEBUG)->end()
                                    ->scalarNode('bubble')->defaultTrue()->end()
                                    ->scalarNode('type')
                                        ->defaultValue('trace')
                                        ->validate()
                                            ->ifNotInArray($allowedLoggerTypes)
                                            ->thenInvalid(\sprintf('Allowed types: [%s]', \implode(', ', $allowedLoggerTypes)))
                                        ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
