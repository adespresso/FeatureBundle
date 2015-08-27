<?php

namespace Ae\FeatureBundle\Service;

use Ae\FeatureBundle\Entity\FeatureManager;
use Ae\FeatureBundle\Security\FeatureSecurity;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class Feature
{
    protected $security;
    protected $manager;

    /**
     * @param \Ae\FeatureBundle\Entity\FeatureManager    $manager
     * @param \Ae\FeatureBundle\Security\FeatureSecurity $security
     */
    public function __construct(FeatureManager $manager, FeatureSecurity $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    /**
     * Check if a feature (defined by name/parent) is granted to the logged user.
     *
     * @param string $name
     * @param string $parent
     *
     * @return bool
     */
    public function isGranted($name, $parent)
    {
        try {
            return $this->security->isGranted($this->manager->find($name, $parent));
        } catch (\Exception $e) {
            return false;
        }
    }
}
