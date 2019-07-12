<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class TagwalkApiClientExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');
        $api = $config['api'];
        if (!isset($api['host_url'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.host_url" config option must be set'
            );
        }
        if (!isset($api['client_id'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.client_id" config option must be set'
            );
        }
        if (!isset($api['client_secret'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.client_secret" config option must be set'
            );
        }
        $definition = $container->getDefinition(ApiProvider::class);
        $definition->replaceArgument('$baseUri', $api['host_url']);
        $definition->replaceArgument('$clientId', $api['client_id']);
        $definition->replaceArgument('$clientSecret', $api['client_secret']);
        if (isset($api['timeout'])) {
            $definition->replaceArgument('$timeout', $api['timeout']);
        }
        if (isset($api['light'])) {
            $definition->replaceArgument('$lightData', $api['light']);
        }
        if (isset($api['analytics'])) {
            $definition->replaceArgument('$analytics', $api['analytics']);
        }
        if (isset($api['cache_directory'])) {
            $definition->replaceArgument('$cacheDirectory', $api['cache_directory']);
        }
    }
}
