<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected UserPasswordHasherInterface $encoder;
    protected SluggerInterface $slugger;
    public function __construct(UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $user = new User();
        $user->setEmail('email@email.com')->setFirstName('Login')->setLastName('Test')->setPassword('password');
        $password = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $manager->persist($user);


        for ($c = 0; $c < 5; $c++) {
            $category = new Category();
            $category->setName($faker->name);
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            for ($a = 0; $a < 5; $a++) {
                $article = new Article();
                $article->setName($faker->name);
                $article->setSlug(strtolower($this->slugger->slug($article->getName())));
                $article->setCategory($category);
                $article->setDescription($faker->text);
                $article->setQuantity(mt_rand(1, 20));
                $article->setPrice(mt_rand(1000, 2000));
                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
