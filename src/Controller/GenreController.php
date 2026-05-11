<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/genres')]
#[IsGranted('ROLE_ADMIN')]
class GenreController extends AbstractController
{
    #[Route('', name: 'app_genre_index', methods: ['GET'])]
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->render('genre/index.html.twig', [
            'genres' => $genreRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_genre_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($genre);
            $em->flush();
            $this->addFlash('success', 'Genre créé.');

            return $this->redirectToRoute('app_genre_index');
        }

        return $this->render('genre/form.html.twig', [
            'form' => $form,
            'titre_page' => 'Nouveau genre',
        ]);
    }
}
