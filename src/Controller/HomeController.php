<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(LivreRepository $LivreRepository): Response
    {
        $livres = $LivreRepository->findLatest();

        return $this->render('home/index.html.twig', [
            'livres' => $livres,
        ]);
    }
}
