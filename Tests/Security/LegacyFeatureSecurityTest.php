<?php

namespace Ae\FeatureBundle\Tests\Security;

use Ae\FeatureBundle\Security\FeatureSecurity;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Emanuele Minotto <emanuele@adespresso.com>
 * @covers Ae\FeatureBundle\Security\FeatureSecurity
 */
class LegacyFeatureSecurityTest extends FeatureSecurityTest
{
    protected function setUp()
    {
        $context = $this
            ->getMockBuilder(SecurityContextInterface::class)
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
}
