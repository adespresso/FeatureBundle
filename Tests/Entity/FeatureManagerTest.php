<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Configuration;
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
        $this->manager = new FeatureManager($this->em);
    }

    public function testCreate()
    {
        $name = 'foo';
        $parent = $this
            ->getMockBuilder(Feature::class)
            ->getMock();

        $feature = $this->manager->create($name, $parent);
        $this->assertEquals($name, $feature->getName($name));
        $this->assertEquals($parent, $feature->getParent());
    }

    public function testUpdate()
    {
        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('getName')
            ->willReturn('feature');

        $parent = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $parent
            ->expects($this->once())
            ->method('getName')
            ->willReturn('parent');

        $feature
            ->expects($this->once())
            ->method('getParent')
            ->willReturn($parent);

        $cache = $this
            ->getMockBuilder(Cache::class)
            ->getMock();
        $cache
            ->expects($this->once())
            ->method('delete');

        $configuration = $this
            ->getMockBuilder(Configuration::class)
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getResultCacheImpl')
            ->willReturn($cache);

        $this->em
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration);

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($feature);
        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->update($feature);
    }

    public function testUpdateWithoutCache()
    {
        $feature = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $feature
            ->expects($this->once())
            ->method('getName')
            ->willReturn('feature');

        $parent = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $parent
            ->expects($this->once())
            ->method('getName')
            ->willReturn('parent');

        $feature
            ->expects($this->once())
            ->method('getParent')
            ->willReturn($parent);

        $configuration = $this
            ->getMockBuilder(Configuration::class)
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getResultCacheImpl')
            ->willReturn(null);

        $this->em
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration);

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
