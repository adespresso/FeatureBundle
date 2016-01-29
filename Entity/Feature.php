<?php

namespace Ae\FeatureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feature entity.
 *
 * @ORM\Table(name="application_feature")
 * @ORM\Entity
 */
class Feature
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=250)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $role;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Feature", mappedBy="parent")
     */
    private $children;

    /**
     * @var Feature
     *
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="children", cascade={"persist", "remove"})
     */
    private $parent;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enabled  = false;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent.
     *
     * @param Feature $parent
     */
    public function setParent(Feature $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent.
     *
     * @return Feature
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set role.
     *
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getParentRole()
    {
        return $this->getParent() ? $this->getParent()->getRole() : null;
    }

    /**
     * Add children.
     *
     * @param Feature $children
     */
    public function addFeature(Feature $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children.
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function isEnabled()
    {
        return $this->getEnabled() && ($this->getParent() ? $this->getParent()->getEnabled() : true);
    }
}
