<?php

namespace Ae\FeatureBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Ae\FeatureBundle\Entity\FeatureManager;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureAdmin extends Admin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('name')
                ->add('enabled')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('role')
            ->add('enabled')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit'   => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $roles = $this->getRoles();

        $formMapper
            ->add('name', 'text', array(
                'required' => true,
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
            ))
            ->add('role', 'choice', array(
                'choices'  => array_combine($roles, $roles),
                'multiple' => false,
                'required' => false,
            ))
        ;
        if (!$this->getSubject()->getParent()) {
            $formMapper
                ->add('children', 'sonata_type_collection', array(
                      'required' => false,
                  ), array(
                      'edit'     => 'inline',
                      'inline'   => 'table',
                  ))
            ;
        }
    }

    protected function getRoles()
    {
        $roleHierarchy = $this->getConfigurationPool()->getContainer()->getParameter('security.role_hierarchy.roles');

        $roles   = array_keys($roleHierarchy);
        $roles[] = 'ROLE_PREVIOUS_ADMIN';

        return $roles;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        if ($context === 'list') {
            $query->andWhere(current($query->getDQLPart('from'))->getAlias().'.parent IS NULL');
        }

        return $query;
    }

    public function postUpdate($object)
    {
        $cache = $this->modelManager->getEntityManager($object)->getConfiguration()->getResultCacheImpl();
        foreach ($object->getChildren() as $child) {
            $cache->delete($this->getObjectCacheKey($child));
        }
    }

    protected function getObjectCacheKey($object)
    {
        return FeatureManager::generateCacheKey($object->getParent()->getName(), $object->getName());
    }
}
