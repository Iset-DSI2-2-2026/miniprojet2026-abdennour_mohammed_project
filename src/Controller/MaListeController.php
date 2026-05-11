<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Service\MaListeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MaListeController extends AbstractController
{
    #[Route('/ma-liste', name: 'app_ma_liste')]
    public function index(MaListeService $maListeService, LivreRepository $livreRepository): Response
    {
        $livres = $livreRepository->findByIdsOrdered($maListeService->getIds());

        return $this->render('ma_liste/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/livres/{id}/ma-liste/ajouter', name: 'app_ma_liste_ajouter', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function ajouter(int $id, MaListeService $maListeService, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);
        if (!$livre) {
            throw $this->createNotFoundException();
        }

        $maListeService->add($id);
        $this->addFlash('success', 'Livre ajouté à votre liste de lecture.');

        return $this->redirectToRoute('app_livre_show', ['id' => $id]);
    }

    #[Route('/ma-liste/{id}/retirer', name: 'app_ma_liste_retirer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function retirer(Request $request, int $id, MaListeService $maListeService): Response
    {
        if (!$this->isCsrfTokenValid('ma_liste_remove'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_ma_liste');
        }

        $maListeService->remove($id);
        $this->addFlash('success', 'Livre retiré de votre liste.');

        return $this->redirectToRoute('app_ma_liste');
    }
}
