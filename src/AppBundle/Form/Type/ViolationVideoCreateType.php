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
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
            ->add('date', 'date', [
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('carNumber', 'number', [
                'required' => false,
            ])
            ->add('save', 'submit', [
                'label' => 'Create',
                'attr'  => [
                    'class' => 'btn-primary',
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