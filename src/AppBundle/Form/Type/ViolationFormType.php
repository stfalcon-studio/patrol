<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ViolationFormType
 */
class ViolationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('photo', 'file', [
                'constraints' => [
                    new NotBlank(),
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
        return 'violation_photo_form';
    }

}