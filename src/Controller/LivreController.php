<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreFilterType;
use App\Form\LivreType;
use App\Mail\LivreCreatedMailSender;
use App\Repository\LivreRepository;
use App\Security\Voter\LivreVoter;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/livres')]
class LivreController extends AbstractController
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly LivreCreatedMailSender $livreCreatedMailSender,
    ) {
    }

    #[Route('', name: 'app_livre_index', methods: ['GET'])]
    public function index(Request $request, LivreRepository $livreRepository, PaginatorInterface $paginator): Response
    {
        $filterForm = $this->createForm(LivreFilterType::class);
        $filterData = $request->query->all()[$filterForm->getName()] ?? null;
        $filterForm->submit(\is_array($filterData) ? $filterData : [], false);

        $disponible = null;
        if ($filterForm->get('disponible')->getData()) {
            $disponible = true;
        }

        $qb = $livreRepository->createFilteredQueryBuilder(
            $filterForm->get('titre')->getData(),
            $filterForm->get('genre')->getData(),
            $disponible,
            $filterForm->get('tag')->getData(),
        );

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10,
        );

        return $this->render('livre/index.html.twig', [
            'livres' => $pagination,
            'filterForm' => $filterForm,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/nouveau', name: 'app_livre_nouveau', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BIBLIOTHECAIRE')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();
        $livre->setDisponible(true);
        $livre->setAjoutePar($this->getUser());

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $livre->setImageName($this->fileUploader->upload($imageFile));
            }

            $em->persist($livre);
            $em->flush();

            $this->livreCreatedMailSender->send($livre);

            $this->addFlash('success', 'Le livre a été créé avec succès.');

            return $this->redirectToRoute('app_livre_index');
        }

        return $this->render('livre/form.html.twig', [
            'form' => $form,
            'titre_page' => 'Nouveau livre',
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_livre_modifier', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted(LivreVoter::EDIT, 'livre')]
    public function modifier(Request $request, Livre $livre, EntityManagerInterface $em): Response
    {
        $oldImage = $livre->getImageName();

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                if ($oldImage) {
                    $this->fileUploader->remove($oldImage);
                }
                $livre->setImageName($this->fileUploader->upload($imageFile));
            }

            $em->flush();

            $this->addFlash('success', 'Le livre a été mis à jour.');

            return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
        }

        return $this->render('livre/form.html.twig', [
            'form' => $form,
            'titre_page' => 'Modifier le livre',
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_livre_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(LivreVoter::DELETE, 'livre')]
    public function supprimer(Request $request, Livre $livre, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$livre->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_livre_index');
        }

        if ($livre->getImageName()) {
            $this->fileUploader->remove($livre->getImageName());
        }

        $em->remove($livre);
        $em->flush();

        $this->addFlash('success', 'Le livre a été supprimé.');

        return $this->redirectToRoute('app_livre_index');
    }
}
