<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class WelcomeController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/welcome', name: 'app_welcome')]
    public function index(): Response
    {
        return new Response($this->twig->render('welcome/index.html.twig'));
    }
}
