<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class ViolationAdmin
 */
class ViolationAdmin extends Admin
{
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('approved')
            ->add('carNumber')
            ->add('author', 'entity', array('class' => 'AppBundle\Entity\User'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('approved')
            ->add('carNumber')
            ->add('author', 'entity', array('class' => 'AppBundle\Entity\User'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('carNumber')
            ->add('author');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('carNumber')
            ->add('approved')
            ->add('author')
            ->add('_action', 'actions', [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ],
            ]);
    }

}