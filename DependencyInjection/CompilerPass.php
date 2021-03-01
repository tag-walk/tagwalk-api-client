<?php

namespace Tagwalk\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $map = [
            'cache'                 => 'tagwalk.cache.api',
            'access_token_storage'  => 'tagwalk.storage.access_token',
            'refresh_token_storage'  => 'tagwalk.storage.refresh_token',
        ];

        $config = $container->getExtensionConfig('tagwalk_api_client')[0];

        foreach ($map as $configKey => $storageProxyName) {
            $definition = $container->getDefinition($storageProxyName);

            if (isset($config['storage_prefix'])) {
                $prefix = $config['storage_prefix'] . $definition->getArgument(1);
                $definition->setArgument(1, $prefix);
            }

            if (!isset($config[$configKey])) {
                continue;
            }

            $definition->replaceArgument(0, new Reference($config[$configKey]));
        }
    }
}
