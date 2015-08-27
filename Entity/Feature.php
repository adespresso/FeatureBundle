<?php

namespace Ae\FeatureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ae\FeatureBundle\Entity\Feature.
 *
 * @ORM\Table(name="application_feature")
 * @ORM\Entity
 */
class Feature
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var text
     *
     * @ORM\Column(name="role", type="text", nullable=true)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="Feature", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="children", cascade={"persist", "remove"})
     **/
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
     * @param Ae\FeatureBundle\Entity\Feature $parent
     */
    public function setParent(\Ae\FeatureBundle\Entity\Feature $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent.
     *
     * @return mixed
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
     * @param text $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role.
     *
     * @return text
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
     * @param Ae\FeatureBundle\Entity\Feature $children
     */
    public function addFeature(\Ae\FeatureBundle\Entity\Feature $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children.
     *
     * @return Doctrine\Common\Collections\Collection
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
