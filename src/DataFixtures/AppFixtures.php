<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected UserPasswordHasherInterface $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('email@email.com')->setFirstName('Login')->setLastName('Test')->setPassword('password');
        $password = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();
    }
}
