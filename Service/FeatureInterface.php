<?php

namespace Ae\FeatureBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Simone Di Maulo <simone@adespresso.com>
 */
interface FeatureInterface
{
    /**
     * Check if a feature (defined by name/parent) is granted to the logged user.
     *
     * @param string $name
     * @param string $parent
     *
     * @return bool
     */
    public function isGranted($name, $parent);

    public function isGrantedForUser(string $name, string $parent, UserInterface $user): bool;
}
