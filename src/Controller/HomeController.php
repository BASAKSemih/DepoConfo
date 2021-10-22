<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homePage")
     */
    public function home(CategoryRepository $categoryRepository, ArticleRepository $articleRepository): Response
    {
        $bestarticles = $articleRepository->findByIsBest(1);
        $articles = $articleRepository->findAll();
        $categorys = $categoryRepository->findAll();
        return $this->render('home/home.html.twig', [
            'categorys' => $categorys,
            'articles' => $articles,
            'bestarticles' => $bestarticles
        ]);
    }
}
