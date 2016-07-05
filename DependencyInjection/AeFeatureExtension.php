<?php

namespace Ae\FeatureBundle\DependencyInjection;

use Ae\FeatureBundle\Admin\FeatureAdmin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        if ($this->canLoadSonataAdmin($container)) {
            $loader->load('sonata.xml');
        }
    }

    /**
     * Checks if the Admin class can be loaded without conflicts.
     *
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    private function canLoadSonataAdmin(ContainerBuilder $container)
    {
        if (!class_exists('Sonata\AdminBundle\Admin\Admin')) {
            return false;
        }

        $conflicts = array_filter(
            $container->getDefinitions(),
            function (Definition $definition) {
                return FeatureAdmin::class === $definition->getClass() &&
                    $definition->hasTag('sonata.admin');
            }
        );

        return empty($conflicts);
    }
}
