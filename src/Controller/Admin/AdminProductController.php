<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/produits')]
class AdminProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'admin_products')]
    public function index(): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $this->productRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'admin_product_new')]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Produit créé.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/form.html.twig', ['form' => $form, 'title' => 'Nouveau produit']);
    }

    #[Route('/{id}/modifier', name: 'admin_product_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) throw $this->createNotFoundException();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Produit modifié.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/form.html.twig', ['form' => $form, 'title' => 'Modifier le produit']);
    }

    #[Route('/{id}/supprimer', name: 'admin_product_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $product = $this->productRepository->find($id);
        if ($product) {
            $this->em->remove($product);
            $this->em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('admin_products');
    }
}
