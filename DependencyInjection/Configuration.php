<?php

/** @noinspection ALL */

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
            ->scalarNode('client_id')->end()
            ->scalarNode('client_secret')->end()
            ->scalarNode('host_url')->defaultValue('https://test.api.tag-walk.com')->end()
            ->floatNode('timeout')->defaultValue(30.0)->min(0.0)->max(60.0)->end()
            ->booleanNode('analytics')->defaultFalse()->end()
            ->booleanNode('light')->defaultFalse()->end()
            ->scalarNode('showroom')->defaultNull()->end()
            ->scalarNode('redirect_url')->defaultNull()->end()
            ->scalarNode('authorization_url')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
