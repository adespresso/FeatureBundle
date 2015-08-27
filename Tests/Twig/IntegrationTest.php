<?php

namespace Ae\FeatureBundle\Tests\Twig;

use Ae\FeatureBundle\Twig\Extension\FeatureExtension;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    public function getExtensions()
    {
        return array(
            $this->getFeatureExtension(),
        );
    }

    public function getFixturesDir()
    {
        return dirname(__FILE__).'/Fixtures/';
    }

    protected function getFeatureExtension()
    {
        $service = $this->getMockBuilder('Ae\FeatureBundle\Service\Feature')
            ->disableOriginalConstructor()
            ->getMock();
        $service->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap(array(
                array('featureA', 'group', true),
                array('featureB', 'group', false),
            )));

        return new FeatureExtension($service);
    }
}
