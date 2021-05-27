<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Article;
use App\Form\EditPhotoType;


class MainController extends AbstractController
{
    /**
     * Controleur de la page d'accueil du site
     * @Route("/", name="main_home")
     */
    public function home(): Response
    {

        $articleRepo= $this->getDoctrine()->getRepository(Article::class);

        // Récupération du paramètre pour savoir combien d'articles seront affichés sur l'acceuil
        $articlesNumber = $this->getParameter('homepage_articles_number');

        // Récupération des derniers articles publiés
        $articles = $articleRepo->findBy([], ['publicationDate' => 'DESC'], $articlesNumber);

        return $this->render('main/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/mon-profil/", name="main_profil")
     * @Security("is_granted('ROLE_USER')")
     */
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }


    /**
     * @Route("/edit-photo/", name="main_edit_photo")
     * @Security("is_granted('ROLE_USER')")
     */
    public function editPhoto(Request $request): Response
    {

        $form = $this->createForm(EditPhotoType::class);

        $form->handleRequest($request);

        // Si le formulaire a été envoyé et n'a pas d'erreur
        if($form->isSubmitted() && $form->isValid()){

            // Récupération du champ photo
            $photo = $form->get('photo')->getData();

            // Récupération de l'emplacement du dossier des photos de profils
            $profilPhotosDirectory = $this->getParameter('users_uploaded_photo_directory');

            // Récupération de l'utilisateur connecté
            $connectedUser = $this->getUser();


            // Si l'utilisateur a déjà une photo de profil, on la supprime
            if($connectedUser->getPhoto() != null){
                unlink( $profilPhotosDirectory . $connectedUser->getPhoto() );
            }

            // Génération d'un nom de fichier jusqu'à en trouver un qui soit dispo
            do{

                $newFileName = md5($connectedUser->getId() . random_bytes(100)) . '.' . $photo->guessExtension();

                dump($newFileName);

            } while( file_exists( $profilPhotosDirectory . $newFileName ) );

            // Mise à jour du nom de la photo de profil de l'utilisateur connecté dans la BDD
            $connectedUser->setPhoto($newFileName);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $photo->move(
                $profilPhotosDirectory,
                $newFileName
            );

            // Message flash de succès et redirection de l'utilisateur
            $this->addFlash('success', 'Photo de profil modifiée avec succès !');

            return $this->redirectToRoute('main_profil');

        }

        return $this->render('main/editPhoto.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
