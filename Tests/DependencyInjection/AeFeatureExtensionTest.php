<?php

namespace Ae\FeatureBundle\Tests\DependencyInjection;

use Ae\FeatureBundle\DependencyInjection\AeFeatureExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Emanuele Minotto <emanuele@adespresso.com>
 * @covers Ae\FeatureBundle\DependencyInjection\AeFeatureExtension
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
     * Test parameters "alias" to migrate from CreativeWeb to AdEspresso.
     *
     * @param string $parameterName
     * @param string $expectedParameterValue
     *
     * @dataProvider parametersProvider
     * @group legacy
     */
    public function testLegacyParameters($parameterName, $expectedParameterValue)
    {
        $this->load();
        $this->compile();

        $parameterName = 'cw' . substr($parameterName, 2);
        $this->assertContainerBuilderHasParameter($parameterName, $expectedParameterValue);
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

        $this->assertContainerBuilderHasParameter($parameterName, $expectedParameterValue);
    }

    /**
     * @return array
     */
    public function parametersProvider()
    {
        return [
            ['ae_feature.manager.class', 'Ae\FeatureBundle\Entity\FeatureManager'],
            ['ae_feature.security.class', 'Ae\FeatureBundle\Security\FeatureSecurity'],
            ['ae_feature.feature.class', 'Ae\FeatureBundle\Service\Feature'],
            ['ae_feature.twig.extension.feature.class', 'Ae\FeatureBundle\Twig\Extension\FeatureExtension'],
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

        $oldServiceId = 'cw' . substr($serviceId, 2);
        $this->assertContainerBuilderHasService($serviceId, $expectedClass);
        $this->assertContainerBuilderHasServiceDefinitionWithParent($oldServiceId, $serviceId);
    }

    /**
     * Test services.
     *
     * @param string $serviceId
     * @param string $expectedClass
     *
     * @dataProvider servicesProvider
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
            ['ae_feature.manager', 'Ae\FeatureBundle\Entity\FeatureManager'],
            ['ae_feature.security', 'Ae\FeatureBundle\Security\FeatureSecurity'],
            ['ae_feature.feature', 'Ae\FeatureBundle\Service\Feature'],
            ['ae_feature.twig.extension.feature', 'Ae\FeatureBundle\Twig\Extension\FeatureExtension'],
        ];
    }
}
