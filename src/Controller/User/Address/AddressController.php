<?php

namespace App\Controller\User\Address;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    protected EntityManagerInterface $entityManger;
    protected AddressRepository $addressRepository;

    public function __construct(EntityManagerInterface $entityManger, AddressRepository $addressRepository)
    {
        $this->entityManger = $entityManger;
        $this->addressRepository = $addressRepository;
    }


    /**
     * @Route("/mon-compte/ajouter-une-adresse", name="user_add_address")
     */
    public function createAddress(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', "Vous devez vous connecter pour faire cette opération");
            return $this->redirectToRoute('app_login');
        }
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * @Route("/mon-compte/mes-adresse", name="address_show")
     */
    public function showAddress()
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', "Vous devez vous connecter pour faire cette opération");
            return $this->redirectToRoute('app_login');
        }
        $address = $this->addressRepository->findByUser($user);
        if (!$address) {
            $this->addFlash('warning', "Vous n'avez pas adresse veuillez en crée une");
            return $this->redirectToRoute('user_add_address');
        }
        return $this->render('user/address/show.html.twig', [
            'address' => $address
        ]);
    }

    /**
     * @Route("/mon-comte/supprimer-une-adresse/{idAddress}", name="delete_address")
     */
    public function deleteaddress($idAddress)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', "Vous devez vous connecter pour faire cette opération");
            return $this->redirectToRoute('app_login');
        }
        $address = $this->addressRepository->findOneById($idAddress);
        if ($address->getUser() === $user) {
            $this->entityManger->remove($address);
            $this->entityManger->flush();
            $this->addFlash('success', "l'Adresse à été supprimer avec succès");
            return $this->redirectToRoute('homePage');
        }
        $this->addFlash('warning', "Cela ne vous appartient pas");
        return $this->redirectToRoute('homePage');
    }
}
