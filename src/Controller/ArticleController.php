<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/{categorySlug}/{articleSlug}", name="article_show", priority="-50")
     */
    public function showArticle(ArticleRepository $articleRepository, $articleSlug)
    {
        $article = $articleRepository->findOneBySlug($articleSlug);
        if (!$article) {
            $this->addFlash('error', "Le produit demander n'existe passss");
            return $this->redirectToRoute('homePage');
        }
        return $this->render('home/article/show.html.twig', [
            'article' => $article
        ]);
    }
}
