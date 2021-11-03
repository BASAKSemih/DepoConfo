<?php

namespace App\Controller\User\Order;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OrderStatusController extends AbstractController
{
    protected OrderRepository $orderRepository;
    protected UserRepository $userRepository;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande/success/{idOrder}", name="success_Order")
     */
    public function successPayment($idOrder)
    {
        if (!$idOrder) {
            $this->addFlash('error', 'Veuuillez contacter le propriétaire du site');
            return $this->redirectToRoute('homePage');
        }

        $order = $this->orderRepository->findOneById($idOrder);
        $order->setIsPaid(1);
        $this->entityManager->flush();


        return $this->render('user/order/success.html.twig');
    }

    /**
     * @Route("/erreur/commmande/{idOrder}", name="error_order")
     */
    public function errorPayment()
    {
        return $this->render('user/order/error.html.twig');
    }
}
