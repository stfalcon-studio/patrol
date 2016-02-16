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
                    new NotBlank([
                        'message' => 'Виберіть відео файл!',
                    ]),
                ],
            ])
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden', [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Поставте мітку на карті!',
                    ]),
                ],
            ])
            ->add('recordingType', 'hidden')
            ->add('date', 'date', [
                'label'    => 'Дата здійснення правопорушення',
                'widget'   => 'single_text',
                'required' => true,
                'attr'     => [
                    'class' => 'form-control',
                ],
            ])
            ->add('carNumber', 'text', [
                'label'    => 'Номер правопорушника',
                'required' => false,
                'attr'     => [
                    'class' => 'form-control',
                ],
            ])
            ->add('author_email', 'email', [
                'label'    => 'Ваша еклектронна пошта',
                'required' => true,
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
            'data_class' => 'AppBundle\Form\Model\ViolationModel',
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
