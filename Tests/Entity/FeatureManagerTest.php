<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Entity\FeatureManager
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
        $this->cache = new ArrayCache();

        $this->manager = new FeatureManager($this->em, $this->cache);
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

    /**
     * @depends testCreate
     */
    public function testFind()
    {
        $feature = $this->getFeature();
        $query = $this->getMockForAbstractClass(AbstractQuery::class, [], '', false, true, true, [
            'setParameters',
            'getSingleResult',
        ]);

        $this->em
            ->expects($this->once())
            ->method('createQuery')
            ->with($this->stringStartsWith('SELECT'))
            ->willReturn($query);

        $query
            ->expects($this->once())
            ->method('setParameters')
            ->with([
                'name' => 'feature',
                'parent' => 'parent',
            ])
            ->willReturn($query);

        $query
            ->expects($this->once())
            ->method('getSingleResult')
            ->willReturn($feature);

        $this->assertSame(
            $feature,
            $this->manager->find('feature', 'parent')
        );
    }

    /**
     * @depends testFind
     */
    public function testFindWithCache()
    {
        $this->testFind();

        $feature = $this->getFeature();
        $found = $this->manager->find('feature', 'parent');

        $this->assertSame($feature->getName(), $found->getName());

        $featureParent = $feature->getParent();
        $foundParent = $found->getParent();

        $this->assertSame($featureParent->getName(), $foundParent->getName());
    }

    /**
     * @depends testCreate
     * @dataProvider updateDataProvider
     */
    public function testUpdate($flush)
    {
        $feature = $this->getFeature();

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($feature);
        $this->em
            ->expects($flush ? $this->once() : $this->never())
            ->method('flush');

        $this->manager->update($feature, $flush);
    }

    public static function updateDataProvider()
    {
        return [
            'persist only' => [false],
            'with flush' => [true],
        ];
    }

    private function getFeature()
    {
        return $this->manager->create(
            'feature',
            $this->manager->create('parent')
        );
    }
}
