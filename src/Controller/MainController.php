<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Article;


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
            'controller_name' => 'MainController',
            'articles' => $articles,
        ]);
    }


}
