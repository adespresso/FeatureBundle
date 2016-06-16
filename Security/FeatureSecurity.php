<?php

namespace Ae\FeatureBundle\Security;

use Ae\FeatureBundle\Entity\Feature;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Controls access to a Feature.
 *
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureSecurity
{
    /**
     * @param AuthorizationCheckerInterface|null
     */
    protected $context;

    /**
     * @param AuthorizationCheckerInterface $context
     */
    public function __construct(AuthorizationCheckerInterface $context = null)
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
