<?php

namespace Ae\FeatureBundle\Tests\Security;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Security\FeatureSecurity;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Security\FeatureSecurity
 */
class FeatureSecurityTest extends PHPUnit_Framework_TestCase
{
    protected $security;

    protected function setUp()
    {
        $context = $this
            ->getMockBuilder(SecurityContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->any())
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

        $feature = $this->getMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $tests[] = [$feature, false];

        $feature = $this->getMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $tests[] = [$feature, true];

        $feature = $this->getMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $feature
            ->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue('ROLE_USER'));
        $tests[] = [$feature, true];

        $feature = $this->getMock(Feature::class);
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
        $tests[] = [$feature, false];

        return $tests;
    }
}
