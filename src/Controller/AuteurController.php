<?php

namespace App\Controller;

use App\Entity\Auteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuteurRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuteurType;

#[Route('admin/auteur', name: 'admin.auteur')]
class AuteurController extends AbstractController
{

    public function __construct(
        private AuteurRepository $repo,
        private EntityManagerInterface $em
    )
    {}

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        $auteurs = $this->repo->findAll();

        return $this->render('backend/auteur/index.html.twig', [
            'auteurs' => $auteurs
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $auteur = new Auteur();

        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $this->em->persist($auteur);
            $this->em->flush();

            $this->addFlash('success', 'Auteur ajouté avec succès');

            return $this->redirectToRoute('admin.auteur.index');
        }

        return $this->render('backend/auteur/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Auteur $auteur, Request $request): Response|RedirectResponse
    {
        if (!$auteur instanceof Auteur) {
            $this->addFlash('error', 'Auteur non trouvé');

            return $this->redirectToRoute('admin.auteur.index');
        }

        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($auteur);
            $this->em->flush();
            $this->addFlash('success', 'Auteur mis à jour avec succès');

            return $this->redirectToRoute('admin.auteur.index');
        }

        return $this->render('backend/auteur/update.html.twig', [
            'form' => $form,
            'auteur' => $auteur,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['GET'])]
    public function delete(?Auteur $auteur): RedirectResponse
    {
        if (!$auteur instanceof Auteur) {
            $this->addFlash('error', 'Auteur non trouvé');

            return $this->redirectToRoute('admin.auteur.index');
        }

        $this->em->remove($auteur);
        $this->em->flush();
        $this->addFlash('success', 'Auteur supprimé avec succès');

        return $this->redirectToRoute('admin.auteur.index');
    }
}
