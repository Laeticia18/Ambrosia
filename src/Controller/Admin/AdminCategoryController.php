<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categories')]
class AdminCategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'admin_categories')]
    public function index(): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'admin_category_new')]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvelle catégorie',
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_category_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) throw $this->createNotFoundException();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier la catégorie',
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_category_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        if ($category) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }

        return $this->redirectToRoute('admin_categories');
    }
}
