<?php

namespace Ae\FeatureBundle\Tests\Twig;

use Ae\FeatureBundle\Service\Feature;
use Ae\FeatureBundle\Twig\Extension\FeatureExtension;
use Twig_Test_IntegrationTestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Twig\Node\FeatureNode
 */
class IntegrationTest extends Twig_Test_IntegrationTestCase
{
    public function getExtensions()
    {
        $service = $this
            ->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap([
                ['featureA', 'group', true],
                ['featureB', 'group', false],
            ]));

        return [
            new FeatureExtension($service),
        ];
    }

    public function getFixturesDir()
    {
        return __DIR__.'/Fixtures';
    }
}
