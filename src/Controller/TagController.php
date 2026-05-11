<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tags')]
#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    #[Route('', name: 'app_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_tag_nouveau', methods: ['GET', 'POST'])]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag créé.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/form.html.twig', [
            'form' => $form,
            'titre_page' => 'Nouveau tag',
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_tag_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprimer(Request $request, Tag $tag, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_tag'.$tag->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_tag_index');
        }

        $em->remove($tag);
        $em->flush();
        $this->addFlash('success', 'Tag supprimé.');

        return $this->redirectToRoute('app_tag_index');
    }
}
