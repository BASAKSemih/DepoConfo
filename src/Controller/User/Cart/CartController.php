<?php

namespace App\Controller\User\Cart;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected ArticleRepository $articleRepository;
    protected CartService $cartService;

    public function __construct(ArticleRepository $articleRepository, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/mon-panier/ajouter/{idArticle}", name="cart_add", requirements={"id":"\d+"})
     */
    public function cartAdd($idArticle, Request $request)
    {
        $article = $this->articleRepository->findOneById($idArticle);
        if (!$article) {
            $this->addFlash('error', "Le produit demander n'existe pas");
            return $this->redirectToRoute('homePage');
        }
        $this->cartService->add($idArticle);
        $this->addFlash('success', "Le produit à été ajouter au panier");
        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }
        return $this->redirectToRoute('article_show', [
            'categorySlug' => $article->getCategory()->getSlug(),
            'articleSlug' => $article->getSlug()
        ]);
    }

    /**
     * @Route("/mon-panier/supprimer/{idArticle}", name="cart_delete", requirements={"id":"\d+"})
     */
    public function deleteItemCart($idArticle)
    {
        $article = $this->articleRepository->findOneById($idArticle);
        if (!$article) {
            $this->addFlash('error', "Le produit demander n'existe pas");
            return $this->redirectToRoute('homePage');
        }
        $this->cartService->remove($idArticle);
        $this->addFlash('success', "Le produit à bien été supprimer du panier");
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/mon-panier/retirer/{idArticle}", name="cart_decrement", requirements={"id":"\d+"})
     */
    public function decrementCartArticle($idArticle)
    {
        $article = $this->articleRepository->findOneById($idArticle);
        if (!$article) {
            $this->addFlash('error', "Le produit demander n'existe pas");
            return $this->redirectToRoute('homePage');
        }
        $this->cartService->decrement($idArticle);
        $this->addFlash("success", "le produit a été décrementer");
        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/mon-panier", name="cart_show")
     */
    public function cartShow()
    {
        $detailedCart = $this->cartService->getDetailedCartItem();
        $total = $this->cartService->getTotal();

        return $this->render('user/cart/cart_show.html.twig', [
            'articles' => $detailedCart,
            'total' => $total
        ]);
    }
}
