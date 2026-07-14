<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe')]
    public function homePage(): Response
    {
        return $this->render('recipe_page/recipe.html.twig');
    }
}

?>
