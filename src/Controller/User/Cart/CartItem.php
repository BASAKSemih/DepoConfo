<?php

namespace App\Controller\User\Cart;

use App\Entity\Article;

class CartItem
{
    public $article;
    public $quantity;

    public function __construct(Article $article, int $quantity)
    {
        $this->article = $article;
        $this->quantity = $quantity;
    }

    public function getTotal(): int
    {
        return $this->article->getPrice() * $this->quantity;
    }
}
