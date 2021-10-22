<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/catégorie/{categorySlug}", name="category_show", priority="-2")
     */
    public function showAllProductFromCategory($categorySlug)
    {
        $category = $this->categoryRepository->findOneBySlug($categorySlug);
        if (!$category) {
            $this->addFlash('warning', "Cette catégorie n'existe pas");
            return $this->redirectToRoute('homePage');
        }
        return $this->render('home/category/index.html.twig', [
            'category' => $category
        ]);
    }
}
