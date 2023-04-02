<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    
    #[Route('/dirigeant', name: 'app_dirigeant')]
    public function dirigeant(): Response
    {
        return $this->render('home/dirigeant.html.twig');
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/valeurs', name: 'app_valeurs')]
    public function valeurs(): Response
    {
        return $this->render('home/valeurs.html.twig');
    }
}
