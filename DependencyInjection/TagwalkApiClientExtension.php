<?php
/**
 * PHP version 7.
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
use Tagwalk\ApiClientBundle\Factory\ClientFactory;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Security\ApiTokenAuthenticator;
use Tagwalk\ApiClientBundle\Security\AuthorizationHelper;

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
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');
        $this->checkRequiredConfig($config);
        // Edit ApiProvider dependency injection
        $apiProviderDefinition = $container->getDefinition(ApiProvider::class);
        $apiProviderDefinition->replaceArgument('$lightData', $config['light']);
        $apiProviderDefinition->replaceArgument('$analytics', $config['analytics']);
        $apiProviderDefinition->replaceArgument('$showroom', $config['showroom']);
        // Edit AuthorizationHelper dependency injection
        $authorizationHelperDefinition = $container->getDefinition(AuthorizationHelper::class);
        $authorizationHelperDefinition->replaceArgument('$authorizationUrl', $config['authorization_url']);
        // Edit ClientFactory dependency injection
        $clientFactoryDefinition = $container->getDefinition(ClientFactory::class);
        $clientFactoryDefinition->replaceArgument('$baseUri', $config['host_url']);
        $clientFactoryDefinition->replaceArgument('$timeout', $config['timeout']);
        $clientFactoryDefinition->replaceArgument('$httpCache', $config['http_cache']);
        // Edit ApiTokenAuthenticator dependency injection
        $apiTokenAuthenticatorDefinition = $container->getDefinition(ApiTokenAuthenticator::class);
        $apiTokenAuthenticatorDefinition->replaceArgument('$clientId', $config['client_id']);
        $apiTokenAuthenticatorDefinition->replaceArgument('$clientSecret', $config['client_secret']);
        $apiTokenAuthenticatorDefinition->replaceArgument('$redirectUri', $config['redirect_url']);
        $apiTokenAuthenticatorDefinition->replaceArgument('$showroom', $config['showroom']);
    }

    /**
     * @param array $config
     */
    private function checkRequiredConfig(array $config): void
    {
        if (!isset($config['host_url'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.host_url" config option must be set'
            );
        }
        if (!isset($config['client_id'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.client_id" config option must be set'
            );
        }
        if (!isset($config['client_secret'])) {
            throw new InvalidArgumentException(
                'The "tagwalk_api_client.api.client_secret" config option must be set'
            );
        }
    }
}
