<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepository,
        private OrderRepository $orderRepository,
        private RequestStack $requestStack,
    ) {}

    #[Route('/commande/valider', name: 'app_order_validate', methods: ['POST'])]
    public function validate(): Response
    {
        $session = $this->requestStack->getSession();
        $cart = $this->cartRepository->findBySessionId($session->getId());

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        // Numéro de commande unique
        $orderNumber = 'AMB-' . date('Y') . str_pad((string)(random_int(1, 9999)), 4, '0', STR_PAD_LEFT);

        $order = new Order();
        $order->setOrderNumber($orderNumber)
            ->setStatus('confirmed')
            ->setTotalPrice((string) $cart->getTotal());

        foreach ($cart->getCartItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem->getProduct())
                ->setQuantity($cartItem->getQuantity())
                ->setUnitPrice($cartItem->getUnitPrice());

            foreach ($cartItem->getSelectedOptions() as $opt) {
                $orderItem->addSelectedOption($opt);
            }

            $order->addOrderItem($orderItem);
            $this->em->persist($orderItem);
        }

        $this->em->persist($order);

        // Vider le panier
        foreach ($cart->getCartItems() as $item) {
            $this->em->remove($item);
        }

        $this->em->flush();

        return $this->redirectToRoute('app_order_confirmation', ['orderNumber' => $orderNumber]);
    }

    #[Route('/commande/confirmation/{orderNumber}', name: 'app_order_confirmation')]
    public function confirmation(string $orderNumber): Response
    {
        $order = $this->orderRepository->findOneBy(['orderNumber' => $orderNumber]);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('order/confirmation.html.twig', ['order' => $order]);
    }
}
