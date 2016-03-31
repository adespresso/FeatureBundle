<?php

namespace Ae\FeatureBundle\Tests\Service;

use Ae\FeatureBundle\Service\Feature;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Service\Feature
 */
class FeatureTest extends PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $security;
    protected $service;

    protected function setUp()
    {
        $this->manager = $this
            ->getMockBuilder('Ae\FeatureBundle\Entity\FeatureManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->security = $this
            ->getMockBuilder('Ae\FeatureBundle\Security\FeatureSecurity')
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new Feature($this->manager, $this->security);
    }

    public function testIsGrantedTrue()
    {
        $featureEnabled = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->manager
            ->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValueMap([
                ['featureA', 'group', $featureEnabled],
            ]));
        $this->security
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap([
                [$featureEnabled, true],
            ]));

        $this->assertTrue($this->service->isGranted('featureA', 'group'));
    }

    public function testIsGrantedFalse()
    {
        $featureDisabled = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->manager
            ->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValueMap([
                ['featureB', 'group', $featureDisabled],
            ]));
        $this->security
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap([
                [$featureDisabled, false],
            ]));

        $this->assertFalse($this->service->isGranted('featureB', 'group'));
    }
}
