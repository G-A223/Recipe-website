<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/comment/add/{recipeId}', name: 'comment_add')]
    #[IsGranted('ROLE_USER')]
    public function addComment(
        int $recipeId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('GET')) {
            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        $content = $request->request->get('content');

        if (empty($content) || strlen(trim($content)) < 1) {
            $this->addFlash('error', 'Комментарий не может быть пустым');
            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        $recipe = $entityManager->getRepository(Recipe::class)->find($recipeId);
        if (!$recipe) {
            $this->addFlash('error', 'Рецепт не найден');
            return $this->redirectToRoute('home');
        }

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setAuthor($this->getUser());
        $comment->setRecipe($recipe);
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Комментарий добавлен!');
        return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
    }

    #[Route('/comment/edit/{id}', name: 'comment_edit')]
    #[IsGranted('ROLE_USER')]
    public function editComment(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = $entityManager->getRepository(Comment::class)->find($id);

        if (!$comment) {
            $this->addFlash('error', 'Комментарий не найден');
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        if ($comment->getAuthor()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Вы не можете редактировать этот комментарий');
            return $this->redirectToRoute('recipe_show', ['id' => $comment->getRecipe()->getId()]);
        }

        if ($request->isMethod('GET')) {
            return $this->redirectToRoute('recipe_show', ['id' => $comment->getRecipe()->getId()]);
        }

        $content = $request->request->get('content');

        if (empty($content) || strlen(trim($content)) < 1) {
            $this->addFlash('error', 'Комментарий не может быть пустым');
            return $this->redirectToRoute('recipe_show', ['id' => $comment->getRecipe()->getId()]);
        }

        $comment->setContent($content);
        $entityManager->flush();

        $this->addFlash('success', 'Комментарий обновлен!');
        return $this->redirectToRoute('recipe_show', ['id' => $comment->getRecipe()->getId()]);
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteComment(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = $entityManager->getRepository(Comment::class)->find($id);

        if (!$comment) {
            $this->addFlash('error', 'Комментарий не найден');
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        if ($comment->getAuthor()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Вы не можете удалить этот комментарий');
            return $this->redirectToRoute('recipe_show', ['id' => $comment->getRecipe()->getId()]);
        }

        $recipeId = $comment->getRecipe()->getId();
        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Комментарий удален!');
        return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
    }
}
