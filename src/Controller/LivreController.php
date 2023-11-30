<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livre;
use App\Form\LivreType;

#[Route('admin/livre', name: 'admin.livre')]
class LivreController extends AbstractController
{
    public function __construct(
        private LivreRepository $repo,
        private EntityManagerInterface $em
    )
    {}

    #[Route('', name: '.index')]
    public function index(): Response
    {
        $livres = $this->repo->findAll();

        return $this->render('backend/livre/index.html.twig', [
            'livres' => $livres
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $livre = new Livre();

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $this->em->persist($livre);
            $this->em->flush();

            $this->addFlash('success', 'Livre ajoutée avec succès');

            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('backend/livre/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Livre $livre, Request $request): Response|RedirectResponse
    {
        if (!$livre instanceof Livre) {
            $this->addFlash('error', 'Livre non trouvé');

            return $this->redirectToRoute('admin.auteur.index');
        }

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($livre);
            $this->em->flush();
            $this->addFlash('success', 'Livre mis à jour avec succès');

            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('backend/livre/update.html.twig', [
            'form' => $form,
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['GET'])]
    public function delete(?Livre $livre): RedirectResponse
    {
        if (!$livre instanceof Livre) {
            $this->addFlash('error', 'Livre non trouvé');

            return $this->redirectToRoute('admin.livre.index');
        }

        $this->em->remove($livre);
        $this->em->flush();
        $this->addFlash('success', 'Livre supprimé avec succès');

        return $this->redirectToRoute('admin.livre.index');
    }

    #[Route('/{id}/show', name: '.show', methods: ['GET'])]
    public function show(?Livre $livre): Response
    {
        if (!$livre instanceof Livre) {
            $this->addFlash('error', 'Livre non trouvé');

            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('backend/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
