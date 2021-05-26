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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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
    public function publicationList(Request $request, PaginatorInterface $paginator) : Response
    {
        // On récupère dans l'URL la données GET['page'] (si elle n'existe pas, la valeur par défaut sera "1")
        $requestedPage = $request->query->getInt('page', 1);

        // Si le numero de page demandné dans l'URL est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        // Récupération du manager général des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête qui servira au paginator pour récupérer les articles de la page courante
        $query = $em->createQuery('SELECT a FROM App\Entity\Article a');

        // on stock dans $articles les 10 articles de la page demandée dans l'url
        $articles = $paginator->paginate(
            $query,                 // requete de selection
            $requestedPage,         // numero de la page actuelle
            10                 // nombre d'article par page
        );




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

    public function publicationView(Article $article): Response
    {
        dump($article);

        return $this->render('blog/publicationView.html.twig', [
            'article'=> $article,
        ]);
    }

    /**
     * page admin qui permettra de modifier un article existant
     * @route("/publication/modifier/{id}/", name="publication_edit")
     * @Security ("is_granted('ROLE_ADMIN')")
     */

    public function publicationEdit(Article $article, Request $request): Response
    {
        // Création du formulaire de modification d'articles ( c'est le même formulaire que celui de la page de création d'un article, sauf qu'il sera déjà remplis avec les données de l'article "$article")
        $form= $this->createForm(ArticleType::class, $article);

        // Liaison des données POST avec le formulaire
        $form->handleRequest($request);

        // Si le formulaire est envoyé et n'a pas d'erreur
        if($form->isSubmitted() && $form->isValid()){

            // Sauvegarde des changements dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Message flash de succès
            $this->addFlash('success','Article modifié avec succès!');

            //redirection vers la page de l'article modifié
            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $article->getSlug(),
            ]);
        }


        // Appel de la vue en envoyant le formulaire à afficher
        return $this->render('blog/publicationEdit.html.twig', [
            'form' => $form->createView(),

            ]);
    }

    /**
     * page admin qui permettra de supprimer un article
     * @Route("/publication/suppression/{id}/", name="publication_delete")
     * @Security ("is_granted('ROLE_ADMIN')")
     */
    public function publicationDelete(Article $article, Request $request): Response
    {
        // Récupération du token csrf dans l'url
        $tokenCSRF = $request->query->get('csrf_token');

        // Vérification que le token est valide
        if( !$this->isCsrfTokenValid(
            'blog_publication_delete' . $article->getId(),
            $tokenCSRF
        )){
            $this->addFlash ('error','Token sécurité invalide, veuillez re-essayer');
        }else{

            // Suppression de l'article
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'La publication a bien été supprimée avec succès!');
        }

        return $this->redirectToRoute('blog_publication_list');

    }

    /**
     * page qui affiche les résultats de recherche du formulaire dans la navbar
     *
     * @Route("/recherche/", name="search")
     */
    public function search(Request $request, PaginatorInterface $paginator): Response
    {
        // On récupère dans l'URL la données GET['page'] (si elle n'existe pas, la valeur par défaut sera "1")
        $requestedPage = $request->query->getInt('page', 1);

        // Si le numero de page demandné dans l'URL est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        // Récupération du manager général des entités
        $em = $this->getDoctrine()->getManager();

        // récupération de la recherche dans le formulaire
        $search =$request->query->get('q');

        // Création de la requete
        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.content LIKE :search ORDER BY a.publicationDate DESC')
            ->setParameters(['search'=> '%'.$search.'%'])
            ;

        // Récupération des articles
        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            15
        );
        // Appel de la vue
        return $this->render('blog/search.html.twig', [
            'articles' => $articles,
        ]);
    }
}
