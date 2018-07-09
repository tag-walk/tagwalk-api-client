<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TagwalkApiClientExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');

        if (!isset($config['client_id'])) {
            throw new \InvalidArgumentException(
                'The "client_id" option must be set'
            );
        }
        if (!isset($config['client_secret'])) {
            throw new \InvalidArgumentException(
                'The "client_secret" option must be set'
            );
        }
        $container->setParameter(
            'tagwalk_api.client_id',
            $config['client_id']
        );
        $container->setParameter(
            'tagwalk_api.client_secret',
            $config['client_secret']
        );
        var_dump($config);
//        $definition = $container->getDefinition('tagwalk.api_provider');
//        $definition->replaceArgument(0, [
//            'clientId' => $config['client_id'],
//            'clientSecret' => $config['client_secret'],
//            'redirectUri' => null,
//        ]);
    }
}