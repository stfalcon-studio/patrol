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
        $image = $this->getSubject();

        $fileFieldOptions = array('required' => false);
        if ($image && ($webPath = $image->getWebPath())) {
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;

            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" style="max-height: 320px; max-width: 480px;"/>';
        }

        $formMapper
            ->add('approved', 'checkbox', [
                'required' => false,
            ])
            ->add('carNumber')
            ->add('photoFileName', 'text', $fileFieldOptions)
            ->add('date', 'date')
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
            ->add('carNumber', 'text', [
                'label'    => 'Номер порушника',
                'editable' => true,
            ])
            ->add('approved', 'boolean', [
                'label'    => 'Підтвердження',
                'editable' => true,
            ])
            ->add('videoImageName', 'text', [
                'label'    => 'Фото/Відео',
                'template' => 'admin/photo-view.html.twig',
            ])
            ->add('recordingType', 'text', [
                'label'    => 'Тип запису',
                'template' => 'admin/uploading-type-view.html.twig',
            ])
            ->add('latitude', 'text', [
                'label'    => 'Широта',
                'editable' => true,
            ])
            ->add('longitude', 'text', [
                'label'    => 'Довгота',
                'editable' => true,
            ])
            ->add('date', 'datetime', [
                'date_format' => 'yyyy-MM-dd HH:mm:ss',
                'label'       => 'Дата здійснення',
            ])
            ->add('author', null, [
                'label' => 'Автор',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'delete' => [],
                ],
            ]);
    }
}
