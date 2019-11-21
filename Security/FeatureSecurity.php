<?php

namespace Ae\FeatureBundle\Security;

use Ae\FeatureBundle\Entity\Feature;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Controls access to a Feature.
 *
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureSecurity implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param AuthorizationCheckerInterface
     */
    protected $context;

    /**
     * @param TokenStorageInterface
     */
    private $storage;

    /**
     * @param string
     */
    private $providerKey;

    public function __construct(
        AuthorizationCheckerInterface $context,
        TokenStorageInterface $storage,
        string $providerKey
    ) {
        $this->context = $context;
        $this->storage = $storage;
        $this->providerKey = $providerKey;
        $this->logger = new NullLogger();
    }

    /**
     * @return bool
     */
    public function isGranted(Feature $feature)
    {
        if ($feature->isExpired()) {
            $message = sprintf(
                'The feature "%s.%s" class is deprecated since %s and should be removed.',
                $feature
                    ->getParent()
                    ->getName(),
                $feature->getName(),
                $feature
                    ->getExpiration()
                    ->format('Y-m-d')
            );

            @trigger_error($message, E_USER_DEPRECATED);
            $this->logger->warning($message);
        }

        // feature is enabled without required roles
        // there's no need to check on user roles
        if (!$feature->requiresRoleCheck()) {
            return $feature->isEnabled();
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

    public function isGrantedForUser(Feature $feature, UserInterface $user): bool
    {
        $oldToken = $this->storage->getToken();

        $this->storage->setToken(new UsernamePasswordToken(
            $user,
            null,
            $this->providerKey,
            $user->getRoles()
        ));

        $granted = $this->isGranted($feature);

        $this->storage->setToken($oldToken);

        return $granted;
    }
}
