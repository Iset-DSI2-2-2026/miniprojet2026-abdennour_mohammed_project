<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/auteurs')]
#[IsGranted('ROLE_ADMIN')]
class AuteurController extends AbstractController
{
    #[Route('', name: 'app_auteur_index', methods: ['GET'])]
    public function index(Request $request, AuteurRepository $auteurRepository, PaginatorInterface $paginator): Response
    {
        $qb = $auteurRepository->createQueryBuilder('a')->orderBy('a.nom', 'ASC')->addOrderBy('a.prenom', 'ASC');
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 5);

        return $this->render('auteur/index.html.twig', [
            'auteurs' => $pagination,
        ]);
    }

    #[Route('/nouveau', name: 'app_auteur_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($auteur);
            $em->flush();
            $this->addFlash('success', 'Auteur créé.');

            return $this->redirectToRoute('app_auteur_index');
        }

        return $this->render('auteur/form.html.twig', [
            'form' => $form,
            'titre_page' => 'Nouvel auteur',
        ]);
    }
}
