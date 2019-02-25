<?php

namespace Ae\FeatureBundle\Tests\Security;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Security\FeatureSecurity;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Security\FeatureSecurity
 */
class FeatureSecurityTest extends PHPUnit_Framework_TestCase
{
    protected $security;

    protected function setUp()
    {
        $context = $this
            ->getMockBuilder(AuthorizationCheckerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValueMap([
                ['ROLE_USER', null, true],
                ['ROLE_ADMIN', null, false],
            ]));
        $this->security = new FeatureSecurity($context);
    }

    /**
     * @dataProvider getFeatures
     */
    public function testIsGranted($feature, $expected)
    {
        $this->assertEquals($expected, $this->security->isGranted($feature));
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        $tests = [];

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(true);
        $tests[] = [$feature, false];

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(true);
        $tests[] = [$feature, true];

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $feature
            ->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue('ROLE_USER'));
        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(true);
        $tests[] = [$feature, true];

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $feature
            ->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue('ROLE_USER'));
        $feature
            ->expects($this->atLeastOnce())
            ->method('getParentRole')
            ->will($this->returnValue('ROLE_ADMIN'));
        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(true);
        $tests[] = [$feature, false];

        return $tests;
    }

    public function testFeaturesWithoutRolesEnabled()
    {
        $context = $this->createMock(AuthorizationCheckerInterface::class);
        $context
            ->expects($this->never())
            ->method('isGranted');
        $security = new FeatureSecurity($context);

        $feature = $this->createMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(false);

        $this->assertTrue($security->isGranted($feature));
    }

    public function testFeaturesWithoutRolesDisabled()
    {
        $context = $this->createMock(AuthorizationCheckerInterface::class);
        $context
            ->expects($this->never())
            ->method('isGranted');
        $security = new FeatureSecurity($context);

        $feature = $this->createMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $feature
            ->expects($this->once())
            ->method('requiresRoleCheck')
            ->willReturn(false);

        $this->assertFalse($security->isGranted($feature));
    }
}
