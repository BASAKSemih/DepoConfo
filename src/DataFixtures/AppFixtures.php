<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        $user = new User();
        $user->setEmail('email@email.com')->setFirstName('Login')->setLastName('Test')->setPassword('password');
        $password = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $manager->persist($user);

        $category_name = ['Canapés', 'Meubles TV', 'Chaises',
            'Lits', 'Commodes', 'Mobilier de jardin', 'Meubles enfant', 'Meubles bébé'];
        $article_name = ['Canapés modulables en tissu', 'Canapés 3 places en tissu',
            "Canapés d'angle en tissu", 'Panneaux pour porte coulissante', 'Lits mezzanine et lits superposés'];

        for ($c = 0; $c < 5; $c++) {
            $category = new Category();
            $category->setName($category_name[array_rand($category_name)]);
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            for ($a = 0; $a < 5; $a++) {
                $article = new Article();
                $article->setName($article_name[array_rand($article_name)]);
                $article->setSlug(strtolower($this->slugger->slug($article->getName())));
                $article->setCategory($category);
                $article->setDescription("lorem lorem lorem");
                $article->setQuantity(mt_rand(1, 20));
                $article->setPrice(mt_rand(1000, 2000));
                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
