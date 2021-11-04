<?php

namespace App\Controller\User\Order;

use App\Controller\User\Cart\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    protected CartService $cartService;

    protected string $publicKey;

    public function __construct(CartService $cartService, string $publicKey)
    {
        $this->publicKey = $publicKey;
        $this->cartService = $cartService;
    }


    /**
     * @Route("/mon-panier/commande/{idOrder}/stripe", name="stripe_session")
     */
    public function stripeSession($idOrder)
    {

        $curl = new \Stripe\HttpClient\CurlClient([CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1]);
        $curl->setEnableHttp2(false);
        \Stripe\ApiRequestor::setHttpClient($curl);

        $detailedCart = $this->cartService->getDetailedCartItem();
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'https://127.0.0.1:8000';


        foreach ($detailedCart as $article) {
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $article->article->getPrice(),
                    'product_data' => [
                        'name' => $article->article->getName(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $article->article->getImage()]
                    ],
                ],
                'quantity' => $article->quantity
            ];
        }
        Stripe::setApiKey($this->publicKey);

        $checkout_session = Session::create([
            'line_items' => [[
                $productStripe
            ]],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . "/commande/success/$idOrder",
            'cancel_url' => $YOUR_DOMAIN . "/erreur/commmande/$idOrder"
        ]);

        return $this->redirect($checkout_session->url);
    }
}
