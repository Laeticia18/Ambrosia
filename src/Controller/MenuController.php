<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Option;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository $productRepository,
        private CartRepository $cartRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/menu', name: 'app_menu')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('menu/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/menu/{slug}', name: 'app_menu_category')]
    public function category(string $slug): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }
        return $this->render('menu/category.html.twig', ['category' => $category]);
    }

    #[Route('/produit/{id}', name: 'app_product', requirements: ['id' => '\d+'])]
    public function product(int $id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }
        return $this->render('menu/product.html.twig', ['product' => $product]);
    }
}
