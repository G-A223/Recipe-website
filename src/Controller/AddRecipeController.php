<?php
namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Step;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AddRecipeController extends AbstractController
{
    #[Route('/admin/add-recipe', name: 'add_recipe')]
    #[IsGranted('ROLE_ADMIN')]
    public function addRecipe(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            $categories = $entityManager->getRepository(Category::class)->findAll();
            $tags = $entityManager->getRepository(Tag::class)->findAll();

            return $this->render('add_recipe/add_recipe.html.twig', [
                'categories' => $categories,
                'tags' => $tags,
            ]);
        }

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $categoryIds = $request->request->all('categories');
        $tagIds = $request->request->all('tags');
        $ingredientNames = $request->request->all('ingredient_name');
        $ingredientAmounts = $request->request->all('ingredient_amount');
        $stepDescriptions = $request->request->all('step_description');

        if (empty($title)) {
            $this->addFlash('error', 'Название рецепта обязательно');
            return $this->redirectToRoute('add_recipe');
        }

        if (empty($description)) {
            $this->addFlash('error', 'Описание рецепта обязательно');
            return $this->redirectToRoute('add_recipe');
        }

        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setDescription($description);
        $recipe->setCreatedAt(new \DateTime());

        $imageFile = $request->files->get('image');
        if ($imageFile) {
            $newFilename = $this->uploadImage($imageFile, 'recipes');
            $recipe->setImage($newFilename);
        }

        if (!empty($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $category = $entityManager->getRepository(Category::class)->find($categoryId);
                if ($category) {
                    $recipe->addCategory($category);
                }
            }
        }

        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $tag = $entityManager->getRepository(Tag::class)->find($tagId);
                if ($tag) {
                    $recipe->addTag($tag);
                }
            }
        }

        $entityManager->persist($recipe);
        $entityManager->flush();

        if (!empty($ingredientNames)) {
            foreach ($ingredientNames as $index => $name) {
                if (!empty($name) && !empty($ingredientAmounts[$index])) {
                    $ingredient = new Ingredient();
                    $ingredient->setName($name);
                    $ingredient->setAmount($ingredientAmounts[$index]);
                    $ingredient->setRecipe($recipe);
                    $entityManager->persist($ingredient);
                }
            }
        }

        if (!empty($stepDescriptions)) {
            $stepNumber = 1;
            foreach ($stepDescriptions as $index => $description) {
                if (!empty($description)) {
                    $step = new Step();
                    $step->setDescription($description);
                    $step->setStepNumber($stepNumber++);
                    $step->setRecipe($recipe);

                    $stepImageFile = $request->files->get('step_image_' . $index);
                    if ($stepImageFile) {
                        $newFilename = $this->uploadImage($stepImageFile, 'steps');
                        $step->setImage($newFilename);
                    }

                    $entityManager->persist($step);
                }
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Рецепт "' . $title . '" успешно создан!');
        return $this->redirectToRoute('home');
    }

    private function uploadImage($file, string $subdirectory): string
    {
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/images/' . $subdirectory;

        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $originalFilename);
        if (empty($safeFilename)) {
            $safeFilename = 'image';
        }
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($uploadsDir, $newFilename);

        return $newFilename;
    }
}
