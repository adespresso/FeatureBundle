<?php

namespace Ae\FeatureBundle\Entity;

use Doctrine\Common\Cache\Cache;
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

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param EntityManager $em
     * @param Cache         $cache
     */
    public function __construct(EntityManager $em, Cache $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * @param string $name   Feature name
     * @param string $parent Parent name
     *
     * @return Feature
     */
    public function find($name, $parent)
    {
        $key = $this->generateCacheKey($parent, $name);

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $result = $this->em
            ->createQuery('SELECT f,p FROM AeFeatureBundle:Feature f JOIN f.parent p WHERE f.name = :name AND p.name = :parent')
            ->setParameters([
                'name' => $name,
                'parent' => $parent,
            ])
            ->getSingleResult();

        $this->cache->save($key, $result, 3600 * 24);

        return $result;
    }

    /**
     * @param string $name Feature name
     *
     * @return Feature
     */
    public function findParent($name)
    {
        return $this->em
            ->createQuery('SELECT f FROM AeFeatureBundle:Feature f WHERE f.name = :name AND f.parent IS NULL')
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
        $this->cache->delete($this->generateCacheKey($parent, $name));
    }

    /**
     * @param string $parentName
     * @param string $name
     *
     * @return string
     */
    private function generateCacheKey($parentName, $name)
    {
        return strtolower(sprintf('feature_%s_%s', $parentName, $name));
    }
}
