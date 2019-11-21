<?php

namespace Ae\FeatureBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AeFeatureExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        $container->setAlias('ae_feature.cache', $config['cache']);
        $container->setParameter('ae_feature.provider_key', $config['provider_key']);

        if (!empty($config['expiration_logger'])) {
            $container
                ->getDefinition('ae_feature.security')
                ->addMethodCall('setLogger', [$config['expiration_logger']]);
        }
    }
}
