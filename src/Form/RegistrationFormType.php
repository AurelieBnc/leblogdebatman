<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse email {{ value }} n\'est pas une adresse email valide'
                    ]),
                    new NotBlank([
                        'message' => 'Merci de renseigner une adresse email'
                    ])
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation',
                'first_options' => [
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/',
                        'message' => 'Votre mot de passe doit contenir au minimum 8 caractère avec obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial'
                    ])
                ],
            ])

            ->add('pseudonym', TextType::class, [
                'label' => 'Pseudonyme',
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 40,
                        'minMessage' => 'Votre pseudonyme doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre pseudonyme doit contenir au maximum {{ limit }} caractères',
                    ]),
                    new NotBlank([
                        'message' => 'Merci de renseigner un pseudonyme',
                    ]),
                ]
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Créer mon compte',
                'attr' => [
                    'class' => 'btn btn-outline-primary col-12'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,

            // TODO: à enlever à la fin
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
