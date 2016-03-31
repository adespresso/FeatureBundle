<?php

namespace Ae\FeatureBundle\Tests\Twig;

use Ae\FeatureBundle\Twig\Extension\FeatureExtension;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Twig\Node\FeatureNode
 */
class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    protected static $originalErrorLevel;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        self::$originalErrorLevel = error_reporting();
        error_reporting(self::$originalErrorLevel & ~E_WARNING);

        parent::__construct($name, $data, $dataName);
    }

    public function tearDown()
    {
        parent::tearDown();

        error_reporting(self::$originalErrorLevel);
    }

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
