<?php

namespace Ae\FeatureBundle\Tests\Security;

use Ae\FeatureBundle\Security\FeatureSecurity;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Security\FeatureSecurity
 */
class FeatureSecurityTest extends \PHPUnit_Framework_TestCase
{
    protected $security;

    protected function setUp()
    {
        $context = $this->getMockBuilder('\Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValueMap(array(
                array('ROLE_USER', null, true),
                array('ROLE_ADMIN', null, false),
            )));
        $this->security = new FeatureSecurity($context);
    }

    /**
     * @dataProvider getFeatures
     */
    public function testIsGranted($feature, $expected)
    {
        $this->assertEquals($expected, $this->security->isGranted($feature));
    }

    public function getFeatures()
    {
        $tests = array();

        $f = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $f->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $tests[] = array($f, false);

        $f = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $f->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $tests[] = array($f, true);

        $f = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $f->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $f->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue('ROLE_USER'));
        $tests[] = array($f, true);

        $f = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $f->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $f->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue('ROLE_USER'));
        $f->expects($this->atLeastOnce())
            ->method('getParentRole')
            ->will($this->returnValue('ROLE_ADMIN'));
        $tests[] = array($f, false);

        return $tests;
    }
}
