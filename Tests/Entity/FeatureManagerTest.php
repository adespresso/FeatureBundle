<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Entity\FeatureManager
 */
class FeatureManagerTest extends PHPUnit_Framework_TestCase
{
    protected $em;
    protected $manager;

    protected function setUp()
    {
        $this->em = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager = $this
            ->getMockBuilder(FeatureManager::class)
            ->setConstructorArgs([$this->em])
            ->setMethods(['emptyCache'])
            ->getMock();
    }

    public function testCreate()
    {
        $name = 'foo';
        $parent = $this->getMock(Feature::class);

        $feature = $this->manager->create($name, $parent);
        $this->assertEquals($name, $feature->getName($name));
        $this->assertEquals($parent, $feature->getParent());
    }

    public function testUpdate()
    {
        $feature = $this->getMock(Feature::class);
        $parent = $this->getMock(Feature::class);
        $feature
            ->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parent));
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($feature);
        $this->em
            ->expects($this->once())
            ->method('flush');
        $this->manager->update($feature);
    }

    public function testGenerateCacheKey()
    {
        $parentName = 'PNAME';
        $name = 'NAME';

        $this->assertEquals(
            'feature_pname_name',
            FeatureManager::generateCacheKey($parentName, $name)
        );
    }
}
