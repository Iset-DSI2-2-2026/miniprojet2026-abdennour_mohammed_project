<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Service\BibliothequeStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BibliothequeStats $stats, LivreRepository $livreRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'stats' => $stats,
            'derniersLivres' => $livreRepository->findLastAdded(5),
        ]);
    }
}
