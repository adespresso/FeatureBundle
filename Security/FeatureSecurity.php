<?php

namespace Ae\FeatureBundle\Security;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Ae\FeatureBundle\Entity\Feature;

/**
 * Controls access to a Feature.
 *
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureSecurity
{
    /**
     * @param SecurityContextInterface|null
     */
    protected $context;

    /**
     * @param SecurityContextInterface $context
     */
    public function __construct(SecurityContextInterface $context = null)
    {
        $this->context = $context;
    }

    /**
     * @param Feature $feature
     *
     * @return bool
     */
    public function isGranted(Feature $feature)
    {
        if (null === $this->context) {
            return false;
        }

        if (!$feature->isEnabled()) {
            return false;
        }

        if ($feature->getRole()) {
            if (!$this->context->isGranted($feature->getRole())) {
                return false;
            }
        }

        if ('' !== trim($feature->getParentRole())) {
            if (!$this->context->isGranted($feature->getParentRole())) {
                return false;
            }
        }

        return true;
    }
}
