<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\Feature;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Entity\Feature
 */
class FeatureTest extends PHPUnit_Framework_TestCase
{
    protected $entity;

    protected function setUp()
    {
        $this->entity = new Feature();
    }

    /**
     * Test parent getter & setter.
     */
    public function testParent()
    {
        $parent = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->entity->setParent($parent);
        $this->assertEquals($parent, $this->entity->getParent());
    }

    public function testChildren()
    {
        $parent = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->entity->addFeature($parent);
        $collection = $this->entity->getChildren();
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\Collection',
            $collection
        );
        $this->assertEquals($parent, $collection->first());
    }

    public function testIsEnabledConstructor()
    {
        $this->assertFalse($this->entity->isEnabled());
    }

    public function testIsEnabledAfterSetEnabled()
    {
        $this->entity->setEnabled(true);
        $this->assertTrue($this->entity->isEnabled());
    }

    public function testIsEnabledWithParentDisabled()
    {
        $this->entity->setEnabled(true);
        $this->entity->setParent(new Feature());
        $this->assertFalse($this->entity->isEnabled());
    }

    public function testIsEnabledWithParentEnabled()
    {
        $parent = new Feature();
        $parent->setEnabled(true);
        $this->entity->setEnabled(true);
        $this->entity->setParent($parent);
        $this->assertTrue($this->entity->isEnabled());
    }

    public function testGetParentRole()
    {
        $parent = new Feature();
        $parent->setRole('ROLE_USER');
        $this->entity->setParent($parent);

        $this->assertEquals('ROLE_USER', $this->entity->getParentRole());
    }

    public function testGetParentRoleWithoutParent()
    {
        $this->assertNull($this->entity->getParentRole());
    }
}
