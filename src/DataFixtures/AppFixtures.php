<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \DateTime;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use Faker;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        // Instanciation du faker qui nous servira à créer des fausess données aléatoires
        $faker = faker\Factory::Create('fr_FR)');

        // Création du compte admin du site
        $admin = new User();

        // hydrattion du compte admin
        $admin
            ->setEmail('admin@gmail.com' )
            ->setRegistrationDate( $faker->dateTimeBetween('-1 year', 'now') )
            ->setPseudonym('Batman')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(
                $this->encoder->encodePassword($admin, 'admin@123')
            )
            ;

        $manager->persist($admin);

        //création de 50 comptes utilisateurs
        for($i = 0; $i <50; $i++){

            //création d'un nouveau compte
            $user = new User();

            $user
                ->setEmail($faker->email)
                ->setRegistrationDate( $faker->dateTimeBetween('-1 year', 'now'))
                ->setPseudonym( $faker->userName)
                ->setPassword(
                    $this->encoder->encodePassword($user, 'admin@123')
                )
                ;
            // persistance de l'utilisateur
            $manager->persist( $user);

            //stockage de tout les utilisateurs dans un array PHP pour créer des commentaires aleatoires
            $users[]= $user;
        }
        //Création de 200 articles
        for($i =0; $i <200; $i++){

            //Création d'un nouvel article
            $article =new Article();

            // hydratation des articles
            $article
                ->setPublicationDate( $faker->dateTimeBetween($admin -> getRegistrationDate(), 'now'))
                ->setAuthor($admin)
                ->setTitle($faker->sentence(10) )
                ->setContent( $faker->paragraph(15) )
                ;

            //persistance des articles
            $manager->persist($article);

            // création entre 0 et 10 commentaires aléatoires par article
            $rand = rand(0,10);

            for($j=0; $j< $rand; $j++){

                //creatioin d'un nouveau commentaire
                $comment = new Comment;

                $comment
                    ->setArticle($article)
                    ->setPublicationDate( $faker->dateTimeBetween('-1 year', 'now'))
                    ->setContent( $faker->paragraph(5))
                    ->setAuthor( $faker->randomElement($users))
                    ;

                $manager->persist($comment);
            }
        }

            $manager->flush();
    }


}

