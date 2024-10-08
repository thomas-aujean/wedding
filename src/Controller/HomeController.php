<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('homepage/home.html.twig');
    }

    #[Route('/home', name: 'home_page')]
    public function homepage(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'title' => 'ici'
        ]);
    }
}
