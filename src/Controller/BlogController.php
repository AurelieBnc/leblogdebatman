<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\ArticleType;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;


/**
 * Controleur de la partie blog du site. Toutes les routes commenceront leur URL par "/blog" et leur nom par "blog_"
 * Class BlogController
 * @package App\Controller
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/nouvelle-publication/", name="new_publication")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function newPublication(Request $request)
    {
        // Création d'un nouvel objet de la classe Article, vide pour le moment
        $newArticle = new Article();



        // Création d'un nouveau formulaire à partir de notre formulaire ArticleType et de notre nouvel article encore vide
        $form = $this->createForm(ArticleType::class, $newArticle);

        // Symfony va remplir $newArticle grâce aux données du formulaire envoyé (accessibles dans l'objet Request, c'est pour ça qu'on doit lui donner)
        $form->handleRequest($request);


        // Pour savoir si le formulaire a été envoyé, on a accès à cette condition :
        if($form->isSubmitted() && $form->isValid()){

            // récupération des informations User
            $user = $this->getUser();

            // Hydratation de l'article avec la date et l'auteur
            $newArticle
                ->setPublicationDate(new DateTime())
                ->setAuthor($user)
            ;

            // récupération du manager des entités et sauvegarde en BDD de $newArticle
            $em = $this->getDoctrine()->getManager();

            $em->persist($newArticle);

            $em->flush();

            // Message flash de succès
            $this->addFlash('success', 'Article publié avec succés !');

            // redirection de l'utilisateur sur la page permettant de visualiser le nouvel article
            return $this->redirectToRoute('blog_publication_view', [
                'slug'=> $newArticle->getSlug(),
            ]);
        }

        // Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $form->createView()
        return $this->render('blog/newPublication.html.twig',[
        'form' => $form->createView()
        ]);
    }


    /**
     * Controleur de la page qui liste les articles du site
     *
     * @Route("/publications/liste/", name="publication_list")
     */
    public function publicationList() : Response
    {
        // Récupération du repository des articles pour pouvoir les récupérer
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleRepo->findAll();

        // Appel de la vue en lui envoyant la liste des articles
        return $this->render('blog/publicationList.html.twig', [
            'articles'=> $articles,
        ]);
    }

    /**
     * Controleur de la page d'un article en détail
     *
     * @Route("/publication/{slug}/", name="publication_view")
     */

    public function publicationView(ARTICLE $article): Response
    {
        dump($article);

        return $this->render('blog/publicationView.html.twig', [
            'article'=> $article,
        ]);
    }

    
}
