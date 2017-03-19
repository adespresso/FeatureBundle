<?php

namespace Ae\FeatureBundle\Tests\Service;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Ae\FeatureBundle\Security\FeatureSecurity;
use Ae\FeatureBundle\Service\Feature as FeatureService;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Service\Feature
 */
class FeatureTest extends PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $security;
    protected $service;

    protected function setUp()
    {
        $this->manager = $this
            ->getMockBuilder(FeatureManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->security = $this
            ->getMockBuilder(FeatureSecurity::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new FeatureService($this->manager, $this->security);
    }

    public function testIsGrantedTrue()
    {
        $featureEnabled = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
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
        $featureDisabled = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
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
