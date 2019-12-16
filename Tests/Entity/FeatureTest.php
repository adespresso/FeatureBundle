<?php

namespace Ae\FeatureBundle\Tests\Entity;

use Ae\FeatureBundle\Entity\Feature;
use DateTime;
use Doctrine\Common\Collections\Collection;
use PHPUnit_Framework_TestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Entity\Feature
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
        $parent = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $this->entity->setParent($parent);
        $this->assertEquals($parent, $this->entity->getParent());
    }

    public function testChildren()
    {
        $parent = $this
            ->getMockBuilder(Feature::class)
            ->getMock();
        $this->entity->addFeature($parent);
        $collection = $this->entity->getChildren();
        $this->assertInstanceOf(Collection::class, $collection);
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

    public function testDescriptionField()
    {
        $this->assertNull($this->entity->getDescription());

        $description = 'test description';

        $this->entity->setDescription($description);

        $this->assertEquals($description, $this->entity->getDescription());
    }

    public function testGetParentRoleWithoutParent()
    {
        $this->assertNull($this->entity->getParentRole());
    }

    public function testHasRole()
    {
        $this->assertFalse($this->entity->hasRole());

        $this->entity->setRole('ROLE_USER');
        $this->assertTrue($this->entity->hasRole());
    }

    public function testHasParentRole()
    {
        $this->assertFalse($this->entity->hasParentRole());

        $parent = new Feature();
        $this->entity->setParent($parent);

        $this->assertFalse($this->entity->hasParentRole());

        $parent->setRole('ROLE_USER');
        $this->assertTrue($this->entity->hasParentRole());
    }

    public function testRequiresRoleCheck()
    {
        $this->assertFalse($this->entity->requiresRoleCheck());

        $parent = new Feature();
        $this->entity->setParent($parent);
        $this->assertFalse($this->entity->requiresRoleCheck());

        $parent->setRole('ROLE1');
        $this->assertTrue($this->entity->requiresRoleCheck());

        $this->entity->setRole('ROLE1');
        $this->entity->setParent(new Feature());
        $this->assertTrue($this->entity->requiresRoleCheck());

        $parent = new Feature();
        $parent->setRole('ROLE2');
        $this->entity->setParent($parent);
        $this->entity->setRole('ROLE1');
        $this->assertTrue($this->entity->requiresRoleCheck());
    }

    public function testExpiration()
    {
        $feature = new Feature();

        $this->assertFalse($feature->hasExpiration());
        $this->assertFalse($feature->isExpired());
        $this->assertNull($feature->getExpiration());

        $expiration = new DateTime('now - 2 days');
        $feature->setExpiration($expiration);

        $this->assertTrue($feature->hasExpiration());
        $this->assertTrue($feature->isExpired());
        $this->assertEquals($expiration, $feature->getExpiration());

        $expiration = new DateTime('now + 2 days');
        $feature->setExpiration($expiration);

        $this->assertTrue($feature->hasExpiration());
        $this->assertFalse($feature->isExpired());
        $this->assertEquals($expiration, $feature->getExpiration());

        $feature->setExpiration(null);

        $this->assertFalse($feature->hasExpiration());
        $this->assertFalse($feature->isExpired());
        $this->assertNull($feature->getExpiration());
    }
}
