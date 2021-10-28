<?php

namespace App\Controller\User\Address;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    protected EntityManagerInterface $entityManger;

    public function __construct(EntityManagerInterface $entityManger)
    {
        $this->entityManger = $entityManger;
    }


    /**
     * @Route("/mon-compte/ajouter-une-adresse", name="user_add_address")
     */
    public function createAddress(Request $request)
    {
        $user = $this->getUser();
        if (!$user){
            $this->addFlash('warning', "Vous devez vous connecter pour faire cette opération");
            return $this->redirectToRoute('app_login');
        }
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($user);
            $this->entityManger->persist($address);
            $this->entityManger->flush();
            $this->addFlash('success', "L'adresse à été ajouter avec succès");
            return $this->redirectToRoute('cart_show');
        }
        return $this->render('user/address/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

}