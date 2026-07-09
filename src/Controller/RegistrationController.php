<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('registration/registration.html.twig');
        }

        $login = $request->request->get('_username');
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $passwordRepeat = $request->request->get('_password_repeat');

        if (empty($login)) {
            return $this->redirectToRoute('registration');
        }

        if (empty($email)) {
            return $this->redirectToRoute('registration');
        }

        if (empty($password)) {
            return $this->redirectToRoute('registration');
        }

        if (strlen($password) < 6) {
            return $this->redirectToRoute('registration');
        }

        if ($password !== $passwordRepeat) {
            return $this->redirectToRoute('registration');
        }

        $existingEmail = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingEmail) {
            return $this->redirectToRoute('registration');
        }

        $existingLogin = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['login' => $login]);

        if ($existingLogin) {
            return $this->redirectToRoute('registration');
        }

        $user = new User();
        $user->setLogin($login);
        $user->setEmail($email);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('login');
    }

}

?>
