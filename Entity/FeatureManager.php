<?php

namespace Ae\FeatureBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    public static function generateCacheKey($parentName, $name)
    {
        return strtolower(sprintf('feature_%s_%s', $parentName, $name));
    }

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $name   Feature name
     * @param string $parent Parent name
     *
     * @return Feature
     */
    public function find($name, $parent)
    {
        return $this->em->createQuery('SELECT f,p FROM AeFeatureBundle:Feature f JOIN f.parent p WHERE f.name = :name AND p.name = :parent')
            ->setParameters(array(
                'name'   => $name,
                'parent' => $parent,
            ))
            ->useResultCache(true, 3600 * 24, self::generateCacheKey($parent, $name))
            ->getSingleResult();
    }

    /**
     * @param string $name Feature name
     *
     * @return Feature
     */
    public function findParent($name)
    {
        return $this->em->createQuery('SELECT f FROM AeFeatureBundle:Feature f WHERE f.name = :name AND f.parent IS NULL')
            ->setParameter('name', $name)
            ->getSingleResult();
    }

    /**
     * @param string $name   Feature name
     * @param string $parent Parent name
     *
     * @return Feature
     */
    public function findOrCreate($name, $parent)
    {
        try {
            $feature = $this->find($name, $parent);
        } catch (NoResultException $e) {
            try {
                $parent = $this->findParent($parent);
            } catch (NoResultException $e) {
                $parent = $this->create($parent);
            }
            $feature = $this->create($name, $parent);

            $this->update($feature);
        }

        return $feature;
    }

    /**
     * @param string  $name   Feature name
     * @param Feature $parent Parent Feature
     *
     * @return Feature
     */
    public function create($name, Feature $parent = null)
    {
        $feature = new Feature();
        $feature->setName($name);
        if ($parent) {
            $feature->setParent($parent);
        }

        return $feature;
    }

    /**
     * @param Feature $feature
     * @param bool    $andFlush
     */
    public function update(Feature $feature, $andFlush = true)
    {
        $this->em->persist($feature);
        if ($andFlush) {
            $this->em->flush();
            $this->emptyCache($feature->getName(), $feature->getParent()->getName());
        }
    }

    /**
     * @todo Move cache logic to a separate class
     *
     * @param string $name
     * @param string $parent
     */
    public function emptyCache($name, $parent)
    {
        $cache = $this->em->getConfiguration()->getResultCacheImpl();
        $cache->delete(self::generateCacheKey($parent, $name));
    }
}
