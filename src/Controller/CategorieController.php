<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('admin/categorie', name: 'admin.categorie')]
class CategorieController extends AbstractController
{
    
    public function __construct(
        private CategorieRepository $repo,
        private EntityManagerInterface $em
    )
    {}

    #[Route('', name: '.index')]
    public function index(): Response
    {
        $categories = $this->repo->findAll();

        return $this->render('backend/categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $this->em->persist($categorie);
            $this->em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès');

            return $this->redirectToRoute('admin.categorie.index');
        }

        return $this->render('backend/categorie/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Categorie $categorie, Request $request): Response|RedirectResponse
    {
        if (!$categorie instanceof Categorie) {
            $this->addFlash('error', 'Categorie non trouvé');

            return $this->redirectToRoute('admin.categorie.index');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($categorie);
            $this->em->flush();
            $this->addFlash('success', 'Categorie mis à jour avec succès');

            return $this->redirectToRoute('admin.categorie.index');
        }

        return $this->render('backend/categorie/update.html.twig', [
            'form' => $form,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['GET'])]
    public function delete(?Categorie $categorie): RedirectResponse
    {
        if (!$categorie instanceof Categorie) {
            $this->addFlash('error', 'Categorie non trouvé');

            return $this->redirectToRoute('admin.categorie.index');
        }

        $this->em->remove($categorie);
        $this->em->flush();
        $this->addFlash('success', 'Categorie supprimé avec succès');

        return $this->redirectToRoute('admin.categorie.index');
    }
}
