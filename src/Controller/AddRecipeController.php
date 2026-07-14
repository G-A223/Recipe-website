<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddRecipeController extends AbstractController
{
    #[Route('/admin/add-recipe', name: 'add_recipe')]
    public function homePage(): Response
    {
        return $this->render('add_recipe/add_recipe.html.twig');
    }
}

?>
