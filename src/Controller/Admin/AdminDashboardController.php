<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
    ) {}

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'categoryCount' => count($this->categoryRepository->findAll()),
            'productCount' => count($this->productRepository->findAll()),
            'orderCount' => count($this->orderRepository->findAll()),
        ]);
    }
}
