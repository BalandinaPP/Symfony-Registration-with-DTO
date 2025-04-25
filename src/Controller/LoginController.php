<?php

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class LoginController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private ValidatorInterface $validator,
        private Environment $twig
    ) {}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $dto = new RegisterUserDTO();
        $errors = [];

        if ($request->isMethod('POST')) {
            $dto->email = $request->request->get('email');
            $dto->password = $request->request->get('password');

            $validationErrors = $this->validator->validate($dto);

            if (count($validationErrors) > 0) {
                return new Response($this->twig->render('login/index.html.twig', [
                    'errors' => $validationErrors,
                ]));
            }

            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $dto->email]);

            if (!$user || !$this->hasher->isPasswordValid($user, $dto->password)) {
                $errors = 'Неверный email или пароль';
            } else {
                return new RedirectResponse('/welcome');
            }
        }

        return new Response($this->twig->render('login/index.html.twig', [
            'errors' => $errors
        ]));
    }
}
