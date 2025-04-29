<?php

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

class LoginController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private FormFactoryInterface $formFactory;
    private Environment $twig;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->em = $em;
        $this->hasher = $hasher;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $dto = new RegisterUserDTO();
        $form = $this->formFactory->create(LoginType::class, $dto);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $dto->email]);

                if (!$user || !$this->hasher->isPasswordValid($user, $dto->password)) {
                    $form->addError(new FormError('Неверный email или пароль.'));
                } else {
                    return new RedirectResponse('/welcome');
                }
            }
        }

        return new Response($this->twig->render('login/index.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
