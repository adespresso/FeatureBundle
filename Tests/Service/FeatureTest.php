<?php

namespace Ae\FeatureBundle\Tests\Service;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Ae\FeatureBundle\Security\FeatureSecurity;
use Ae\FeatureBundle\Service\Feature as FeatureService;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @dataProvider isGrantedForUserDataProvider
     */
    public function testIsGrantedForUser($expected, $feature)
    {
        $name = sha1(mt_rand());
        $parent = sha1(mt_rand());
        $user = $this->createMock(UserInterface::class);

        $this->manager
            ->expects($this->once())
            ->method('find')
            ->with($name, $parent)
            ->willReturn($feature);

        $this->security
            ->expects($expected ? $this->once() : $this->any())
            ->method('isGrantedForUser')
            ->with($feature, $user)
            ->willReturn($expected);

        $this->assertSame($expected, $this->service->isGrantedForUser($name, $parent, $user));
    }

    /**
     * @return array
     */
    public function isGrantedForUserDataProvider()
    {
        return [
            'granted' => [true, $this->createMock(Feature::class)],
            'not granted' => [false, $this->createMock(Feature::class)],
            'no feature' => [false, null],
        ];
    }
}
