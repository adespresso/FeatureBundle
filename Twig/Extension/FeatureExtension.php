<?php

namespace Ae\FeatureBundle\Twig\Extension;

use Ae\FeatureBundle\Service\Feature;
use Ae\FeatureBundle\Twig\TokenParser\FeatureTokenParser;
use Twig_Extension;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureExtension extends Twig_Extension
{
    /**
     * @var Feature
     */
    protected $service;

    /**
     * @param Feature $service
     */
    public function __construct(Feature $service)
    {
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new FeatureTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'feature';
    }

    /**
     * @param string $name
     * @param string $parent
     *
     * @return bool
     */
    public function isGranted($name, $parent)
    {
        return $this->service->isGranted($name, $parent);
    }
}
