<?php

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class RegistrationController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;
    private Environment $twig;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        Environment $twig
    ) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_register', methods: ['GET', 'POST'])]
   public function register(Request $request): Response
{
    $dto = new RegisterUserDTO();

    if ($request->isMethod('POST')) {
        $dto->email = $request->request->get('email');
        $dto->password = $request->request->get('password');

        $errors = $this->validator->validate($dto);

        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $dto->email]);
        if ($existingUser) {
           
            $customError = new ConstraintViolation(
                'Пользователь с таким email уже зарегистрирован.',
                null, [], '', 'email', $dto->email
            );
            $errors->add($customError);
        }

        if (count($errors) > 0) {
            return new Response($this->twig->render('registration/index.html.twig', [
                'errors' => $errors,
            ]));
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $this->em->persist($user);
        $this->em->flush();

        return new RedirectResponse('/welcome');
    }

    return new Response($this->twig->render('registration/index.html.twig'));
}
}
