<?php

namespace Ae\FeatureBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('role')
            ->add('enabled')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $roles = $this->getRoles();

        $formMapper
            ->add('name', 'text', [
                'required' => true,
            ])
            ->add('enabled', 'checkbox', [
                'required' => false,
            ])
            ->add('role', 'choice', [
                'choices' => array_combine($roles, $roles),
                'multiple' => false,
                'required' => false,
            ]);

        if (!$this->getSubject()->getParent()) {
            $formMapper->add(
                'children',
                'sonata_type_collection',
                [
                    'required' => false,
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ]
            );
        }
    }

    protected function getRoles()
    {
        $roleHierarchy = $this
            ->getConfigurationPool()
            ->getContainer()
            ->getParameter('security.role_hierarchy.roles');

        $roles = array_keys($roleHierarchy);
        $roles[] = 'ROLE_PREVIOUS_ADMIN';

        return $roles;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        if ($context === 'list') {
            $alias = current($query->getDQLPart('from'))->getAlias();
            $query->andWhere($alias.'.parent IS NULL');
        }

        return $query;
    }

    public function postUpdate($object)
    {
        $manager = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('ae_feature.manager');

        foreach ($object->getChildren() as $child) {
            $manager->emptyCache(
                $child->getName(),
                $child
                    ->getParent()
                    ->getName()
            );
        }
    }
}
