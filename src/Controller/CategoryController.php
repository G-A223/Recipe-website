<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'category_show')]
    public function showCategory(
        string $slug,
        CategoryRepository $categoryRepository,
        RecipeRepository $recipeRepository
    ): Response {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Категория не найдена');
        }

        $recipes = $recipeRepository->findByCategory($category->getId());

        return $this->render('category/category.html.twig', [
            'category' => $category,
            'recipes' => $recipes,
        ]);
    }
}
