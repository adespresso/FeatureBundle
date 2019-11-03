<?php

namespace Ae\FeatureBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
    private $enabled = false;

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

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiration;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    public function hasRole(): bool
    {
        return !empty($this->role);
    }

    public function hasParentRole(): bool
    {
        return $this->parent instanceof self && $this->parent->hasRole();
    }

    public function requiresRoleCheck(): bool
    {
        return $this->hasRole() || $this->hasParentRole();
    }

    public function getParentRole()
    {
        return $this->getParent() ? $this->getParent()->getRole() : null;
    }

    /**
     * Add children.
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

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setExpiration(?DateTimeInterface $expiration)
    {
        $this->expiration = $expiration;
    }

    public function hasExpiration(): bool
    {
        return $this->expiration instanceof DateTimeInterface;
    }

    public function getExpiration(): ?DateTimeInterface
    {
        return $this->expiration;
    }

    public function isExpired(): bool
    {
        return $this->hasExpiration()
            && $this->expiration < new DateTime();
    }
}
