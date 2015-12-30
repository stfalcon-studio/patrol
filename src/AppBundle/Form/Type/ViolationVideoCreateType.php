<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ViolationVideoCreateType
 */
class ViolationVideoCreateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', 'file', [
                'label'       => 'Відеофайл',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden', [
                'constraints' => [
                    new NotBlank(),
                ],
                'invalid_message' => 'Поставте мітку на карті!',
            ])
            ->add('date', 'datetime', [
                'label'    => 'Дата здійснення правопорушення',
                'widget'   => 'single_text',
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                ],
            ])
            ->add('carNumber', 'number', [
                'label'    => 'Номер правопорушника',
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                ],
            ])
            ->add('save', 'submit', [
                'label' => 'Додати',
                'attr'  => [
                    'class' => 'btn-success form-control',
                ],
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Violation',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'violation_video_form';
    }
}