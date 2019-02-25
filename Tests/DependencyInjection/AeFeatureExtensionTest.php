<?php

namespace Ae\FeatureBundle\Tests\DependencyInjection;

use Ae\FeatureBundle\DependencyInjection\AeFeatureExtension;
use Ae\FeatureBundle\Entity\FeatureManager;
use Ae\FeatureBundle\Security\FeatureSecurity;
use Ae\FeatureBundle\Service\Feature;
use Ae\FeatureBundle\Twig\Extension\FeatureExtension;
use Doctrine\Common\Cache\ArrayCache;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Emanuele Minotto <emanuele@adespresso.com>
 * @covers \Ae\FeatureBundle\DependencyInjection\AeFeatureExtension
 */
class AeFeatureExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return array
     */
    protected function getContainerExtensions()
    {
        return [
            new AeFeatureExtension(),
        ];
    }

    /**
     * Test parameters.
     *
     * @param string $parameterName
     * @param string $expectedParameterValue
     *
     * @dataProvider parametersProvider
     */
    public function testParameters($parameterName, $expectedParameterValue)
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasParameter(
            $parameterName,
            $expectedParameterValue
        );
    }

    /**
     * @return array
     */
    public function parametersProvider()
    {
        return [
            ['ae_feature.manager.class', FeatureManager::class],
            ['ae_feature.security.class', FeatureSecurity::class],
            ['ae_feature.feature.class', Feature::class],
            [
                'ae_feature.twig.extension.feature.class',
                FeatureExtension::class,
            ],
            ['ae_feature.provider_key', 'main'],
        ];
    }

    /**
     * Test services "alias" to migrate from CreativeWeb to AdEspresso.
     *
     * @param string $serviceId
     * @param string $expectedClass
     *
     * @dataProvider servicesProvider
     * @group legacy
     */
    public function testLegacyServices($serviceId, $expectedClass)
    {
        $this->load();
        $this->compile();

        $oldServiceId = 'cw'.substr($serviceId, 2);
        $this->assertContainerBuilderHasService($serviceId, $expectedClass);
        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            $oldServiceId,
            $serviceId
        );
    }

    /**
     * Test services.
     *
     * @param string $serviceId
     * @param string $expectedClass
     *
     * @dataProvider servicesProvider
     * @dataProvider newServicesProvider
     */
    public function testServices($serviceId, $expectedClass)
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasService($serviceId, $expectedClass);
    }

    /**
     * @return array
     */
    public function servicesProvider()
    {
        return [
            ['ae_feature.manager', FeatureManager::class],
            ['ae_feature.security', FeatureSecurity::class],
            ['ae_feature.feature', Feature::class],
            ['ae_feature.twig.extension.feature', FeatureExtension::class],
        ];
    }

    /**
     * @return array
     */
    public function newServicesProvider()
    {
        return [
            ['ae_feature.default_cache', ArrayCache::class],
        ];
    }

    public function testCacheProviderHasDefaultAlias()
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(
            'ae_feature.cache',
            'ae_feature.default_cache'
        );
    }

    public function testCacheProviderHasDefinedAlias()
    {
        $this->load([
            'cache' => 'service',
        ]);

        $this->assertContainerBuilderHasAlias('ae_feature.cache', 'service');
    }

    public function testKeyProviderCanBeDefined()
    {
        $providerKey = 'test_'.sha1(mt_rand());

        $this->load([
            'provider_key' => $providerKey,
        ]);

        $this->assertContainerBuilderHasParameter('ae_feature.provider_key', $providerKey);
    }
}
