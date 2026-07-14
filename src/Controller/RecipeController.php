<?php
namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/{id}', name: 'recipe_show')]
    public function showRecipe(int $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Рецепт не найден');
        }

        return $this->render('recipe_page/recipe.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
