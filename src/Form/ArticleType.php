<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use FOS\CKEditorBundle\Form\Type\CKEditorType;



class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'label'=>'Titre',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Merci de renseigner un titre'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage'=> 'Le titre doit contenir au moins {{ limit }} caractères',
                        'max' => 150,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('content', CKEditorType::class,[
                'label' => 'Contenu',
                'attr' =>[
                    'class'=> 'd-none',
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Merci de renseigner un titre'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage'=> 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'max' => 20000,
                        'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères' ,
                    ]),
                ]
            ])




            ->add('save', SubmitType::class, [ // Ajout d'un champ de type bouton de validation
                'label' => 'Créer article',// Texte du bouton
                'attr'=> [
                    'class'=> 'btn btn-outline-primary col-12'
                ],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,

            //TODO à enlever à la fin
            'attr'=>[
                'novalidate'=>'novalidate',
            ],
        ]);
    }
}
