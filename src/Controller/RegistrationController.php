<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \DateTime;

class RegistrationController extends AbstractController
{
    /**
     * Contrôleur de la page d'inscription
     *
     * @Route("/creer-un-compte/", name="main_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        // Redirige de force vers l'accueil si l'utilisateur est déjà connecté
        if($this->getUser()){
            return $this->redirectToRoute('main_home');
        }

        // Création d'un nouvel utilisateur
        $user = new User();

        // Création d'un formulaire branché sur $user
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Hydratation du formulaire avec les données POST récupérées dans l'objet $request
        $form->handleRequest($request);

        // Si le formulaire est envoyé et sans erreurs
        if($form->isSubmitted() && $form->isValid()){

            // On hydrate l'utilisateur avec le mot de passe hashé et la date d'inscription
            $user
                ->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                )
                ->setRegistrationDate(new DateTime())
            ;

            // Sauvegarde du nouvel utilisateur en bdd via le manager général des entitées
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Création d'un message flash de succès
            $this->addFlash('success','Votre compte a bien été créer !');

            // TODO: envoyer un email

            // redirection de l'utilisateur sur la page de connexion
            return $this->redirectToRoute('main_login');
        }



        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);

    }
}
