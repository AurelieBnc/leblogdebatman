<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',TextareaType::class,[
                'label'=> false,
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Merci de renseigner un contenu',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage'=> 'Le commentaire doit contenir au moins {{ limit }} caractères',
                        'max' => 1000,
                        'maxMessage' => 'Le commentaire doit contenir au maximum {{ limit }} caractères'
                    ])
                ],
                'attr'=>[
                    'rows'=>'10',
                    'placeholder'=>'Laissez votre commentaire ... '
                ] ,
            ])
            ->add('save', SubmitType::class, [ // Ajout d'un champ de type bouton de validation
                'label' => 'Ajouter un commentaire',
                'attr'=> [
                    'class'=> 'btn btn-outline-primary col-12'// Texte du bouton
                ],
            ]);       ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,

            //TODO à enlever à la fin
            'attr'=>[
                'novalidate'=>'novalidate',
            ],
        ]);
    }
}
