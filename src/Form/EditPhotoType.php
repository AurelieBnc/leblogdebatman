<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class EditPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Sélectionnez une nouvelle photo',
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez sélectionner un fichier'
                    ]),
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'L\'image doit être de type jpg ou png',
                        'maxSizeMessage' => 'Fichier trop volumineux {{ size }}{{ suffix }}, la taille maximum est {{ limit }}{{ suffix }}',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-outline-primary col-12',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            // TODO : à virer
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
