<?php

namespace App\Controller\User\Order;

use App\Controller\User\Cart\CartService;
use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\User;
use App\Form\OrderType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected CartService $cartService;
    protected AddressRepository $addressRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartService $cartService,
        AddressRepository $addressRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
    }


    /**
     * @Route("/mon-panier/je-passe-ma-commande", name="order_command")
     */
    public function orderUser(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecter');
            return $this->redirectToRoute('app_login');
        }
        $adresse = $this->addressRepository->findByUser($user);
        if (!$adresse) {
            $this->addFlash('warning', 'Veuillez ajouter une adresse avant de procéder a la commande');
            return $this->redirectToRoute('user_add_address');
        }
        $form = $this->createForm(OrderType::class, null, ['user' => $user])->handleRequest($request);
        $detailedCart = $this->cartService->getDetailedCartItem();
        return $this->render('user/order/index.html.twig', [
            'form' => $form->createView(),
            'detailedCart' => $detailedCart
        ]);
    }


    /**
     * @Route("/mon-panier/je-passe-ma-commande/recapitulatif", name="order_command_recap", methods={"POST"})
     */
    public function orderUserAdd(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var CartService $detailedCart */
        $detailedCart = $this->cartService->getDetailedCartItem();
        $total = $this->cartService->getTotal();
        $form = $this->createForm(OrderType::class, null, ['user' => $user])->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Address $address */
            $address = $form->get('addresses')->getData();

            $order = new Order();
            $order
                ->setUser($user)
                ->setDeliveryAddress("$address")
                ->setIsPaid(0);

            $this->entityManager->persist($order);
            foreach ($detailedCart as $article) {
                $orderDetails = new OrderDetails();

                $orderDetails
                    ->setOrderDetails($order)
                    ->setArticle($article->article->getName())
                    ->setQuantity($article->quantity)
                    ->setPrice($article->article->getPrice())
                    ->setTotal($article->getTotal());
                $this->entityManager->persist($orderDetails);
            }
            $this->entityManager->flush();


            return $this->render('user/order/recap.html.twig', [
                'detailedCart' => $detailedCart,
                'address' => $address,
                'order' => $order
            ]);
        }
        $this->addFlash('error', 'Erreur veuillez ré-essayer');
        return $this->redirectToRoute('cart_show');
    }
}
