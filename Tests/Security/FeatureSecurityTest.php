<?php

namespace Ae\FeatureBundle\Tests\Security;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Security\FeatureSecurity;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Security\FeatureSecurity
 */
class FeatureSecurityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $context;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var FeatureSecurity
     */
    private $security;

    protected function setUp()
    {
        $this->context = $this->createMock(AuthorizationCheckerInterface::class);
        $this->storage = $this->createMock(TokenStorageInterface::class);

        $this->context
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValueMap([
                ['ROLE_USER', null, true],
                ['ROLE_ADMIN', null, false],
            ]));

        $this->security = new FeatureSecurity($this->context, $this->storage, 'test');
    }

    /**
     * @dataProvider getFeatures
     */
    public function testIsGranted($feature, $expected)
    {
        $this->assertEquals($expected, $this->security->isGranted($feature));
    }

    /**
     * @dataProvider getTokenDataProvider
     */
    public function testIsGrantedForUser($token)
    {
        $expected = (bool) mt_rand(0, 1);
        $roles = [
            'ROLE_USER',
        ];
        $providerKey = 'test';

        $feature = $this->createMock(Feature::class);

        $security = $this
            ->getMockBuilder(FeatureSecurity::class)
            ->setConstructorArgs([
                $this->context,
                $this->storage,
                $providerKey,
            ])
            ->setMethods(['isGranted'])
            ->getMock();

        $this->storage
            ->expects($this->at(0))
            ->method('getToken')
            ->willReturn($token);

        $user = $this->createMock(UserInterface::class);
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($roles);

        $security
            ->expects($this->once())
            ->method('isGranted')
            ->with($feature)
            ->willReturn($expected);

        $this->storage
            ->expects($this->at(1))
            ->method('setToken')
            ->with($this->callback(function ($argument) use ($user, $roles, $providerKey) {
                return $argument instanceof UsernamePasswordToken
                    && $argument->getUser() === $user
                    && $argument->getProviderKey() === $providerKey;
            }));

        $this->storage
            ->expects($this->at(2))
            ->method('setToken')
            ->with($token);

        $this->assertEquals($expected, $security->isGrantedForUser($feature, $user));
    }

    /**
     * @return array
     */
    public function getTokenDataProvider()
    {
        return [
            'web request' => [
                $this->createMock(TokenInterface::class),
            ],
            'server process' => [
                null,
            ],
        ];
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
        $storage = $this->createMock(TokenStorageInterface::class);
        $providerKey = 'test';

        $context
            ->expects($this->never())
            ->method('isGranted');
        $security = new FeatureSecurity($context, $storage, $providerKey);

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
        $storage = $this->createMock(TokenStorageInterface::class);
        $providerKey = 'test';

        $context
            ->expects($this->never())
            ->method('isGranted');
        $security = new FeatureSecurity($context, $storage, $providerKey);

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
