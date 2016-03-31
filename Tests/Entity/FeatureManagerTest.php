<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\FeatureManager;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Entity\FeatureManager
 */
class FeatureManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $manager;

    protected function setUp()
    {
        $this->em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager = $this->getMockBuilder('\Ae\FeatureBundle\Entity\FeatureManager')
            ->setConstructorArgs(array($this->em))
            ->setMethods(array('emptyCache'))
            ->getMock();
    }

    public function testCreate()
    {
        $name    = 'foo';
        $parent  = $this->getMock('Ae\FeatureBundle\Entity\Feature');

        $feature = $this->manager->create($name, $parent);
        $this->assertEquals($name, $feature->getName($name));
        $this->assertEquals($parent, $feature->getParent());
    }

    public function testUpdate()
    {
        $feature = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $parent  = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $feature->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parent));
        $this->em->expects($this->once())
            ->method('persist')
            ->with($feature);
        $this->em->expects($this->once())
            ->method('flush');
        $this->manager->update($feature);
    }

    public function testGenerateCacheKey()
    {
        $parentName = 'PNAME';
        $name = 'NAME';

        $this->assertEquals('feature_pname_name', FeatureManager::generateCacheKey($parentName, $name));
    }
}
