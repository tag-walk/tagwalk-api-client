<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('tagwalk_api_client');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('tagwalk_api_client');
        }
        $rootNode
            ->children()
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('client_id')->end()
                        ->scalarNode('client_secret')->end()
                        ->scalarNode('host_url')->end()
                        ->floatNode('timeout')
                            ->defaultValue(10.0)
                            ->min(0)
                            ->max(60)
                        ->end()
                        ->integerNode('cache_ttl')
                            ->defaultValue(600)
                            ->min(0)
                        ->end()
                        ->scalarNode('cache_directory')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('analytics')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
