<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commandes')]
class AdminOrderController extends AbstractController
{
    public function __construct(private OrderRepository $orderRepository) {}

    #[Route('', name: 'admin_orders')]
    public function index(): Response
    {
        $orders = $this->orderRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('admin/order/index.html.twig', ['orders' => $orders]);
    }

    #[Route('/{id}', name: 'admin_order_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $order = $this->orderRepository->find($id);
        if (!$order) throw $this->createNotFoundException();

        return $this->render('admin/order/show.html.twig', ['order' => $order]);
    }
}
