<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \DateTime;
use App\Entity\User;
use App\Entity\Article;
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
    }
}
