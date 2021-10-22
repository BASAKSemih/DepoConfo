<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;
    protected UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }


    /**
     * @Route("/inscription", name="app_register")
     */
    public function registration(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search_email = $this->userRepository->findOneByEmail($user->getEmail());
            if (!$search_email) {
                $passwordHash = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($passwordHash);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre compte à bien été créee');
                return $this->redirectToRoute('homePage');
            }
            $this->addFlash('warning', 'Cette adresse email est déjà utiliser');
            $form = $this->createForm(UserType::class, $user)->handleRequest($request);
            return $this->render('user/register.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
