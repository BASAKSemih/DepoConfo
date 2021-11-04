<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    protected AddressRepository $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }


    /**
     * @Route("/mon-compte", name="account-show")
     */
    public function accountManager()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', "Veuillez vous connecter");
            return $this->redirectToRoute('app_login');
        }
        $address = $this->addressRepository->findByUser($user);
        if (!$address) {
            return $this->render('user/account/index.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('user/account/index.html.twig', [
            'user' => $user,
            'address' => $address
        ]);
    }

    //TODO VOIR MES COMMANDES, VOIR LE STATUS DES COMMANDES ECT.. ECT.
}
