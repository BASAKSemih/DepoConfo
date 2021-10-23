<?php

namespace App\Controller\User\Cart;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected SessionInterface $session;
    protected ArticleRepository $articleRepository;


    public function __construct(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;
    }

    protected function getCart()
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function add(int $idArticle)
    {
        $cart = $this->getCart();
        if (!array_key_exists($idArticle, $cart)) {
            $cart[$idArticle] = 0;
        }
        $cart[$idArticle]++;
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $idArticle => $quantity) {
            $article = $this->articleRepository->findOneById($idArticle);
            if (!$article) {
                continue;
            }
            $total += $article->getPrice() * $quantity;
        }
        return $total;
    }

    public function remove(int $idArticle)
    {
        $cart = $this->getCart();
        unset($cart[$idArticle]);
        $this->saveCart($cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function getDetailedCartItem(): array
    {
        $detailedCart = [];
        foreach ($this->getCart() as $idArticle => $quantity) {
            $article = $this->articleRepository->findOneById($idArticle);
            if (!$article) {
                continue;
            }
            $detailedCart[] = new CartItem($article, $quantity);
        }
        return $detailedCart;
    }

    public function decrement(int $idArticle)
    {
        $cart = $this->getCart();
        if (!array_key_exists($idArticle, $cart)) {
            return;
        }
        if ($cart[$idArticle] === 1) {
            $this->remove($idArticle);
            return;
        }
        $cart[$idArticle]--;
        $this->saveCart($cart);
    }
}
