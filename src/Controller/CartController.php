<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\OptionRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepository,
        private CartItemRepository $cartItemRepository,
        private ProductRepository $productRepository,
        private OptionRepository $optionRepository,
        private RequestStack $requestStack,
    ) {}

    private function getOrCreateCart(): Cart
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session->getId();

        $cart = $this->cartRepository->findBySessionId($sessionId);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }

    #[Route('/panier', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->getOrCreateCart();
        return $this->render('cart/index.html.twig', ['cart' => $cart]);
    }

    #[Route('/panier/ajouter', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $productId = (int) $request->request->get('product_id');
        $optionIds = $request->request->all('options') ?: [];

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $selectedOptions = [];
        $extraTotal = 0.0;
        foreach ($optionIds as $optId) {
            $opt = $this->optionRepository->find((int) $optId);
            if ($opt) {
                $selectedOptions[] = $opt;
                $extraTotal += (float) $opt->getExtraPrice();
            }
        }

        $unitPrice = (float) $product->getBasePrice() + $extraTotal;

        $cart = $this->getOrCreateCart();

        // Chercher si un CartItem identique existe déjà
        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $itemOptionIds = $item->getSelectedOptions()->map(fn($o) => $o->getId())->toArray();
                $newOptionIds = array_map(fn($o) => $o->getId(), $selectedOptions);
                sort($itemOptionIds);
                sort($newOptionIds);
                if ($itemOptionIds === $newOptionIds) {
                    $existingItem = $item;
                    break;
                }
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product)
                ->setQuantity(1)
                ->setUnitPrice((string) $unitPrice);

            foreach ($selectedOptions as $opt) {
                $cartItem->addSelectedOption($opt);
            }

            $cart->addCartItem($cartItem);
            $this->em->persist($cartItem);
        }

        $this->em->flush();

        $this->addFlash('success', sprintf('"%s" ajouté au panier !', $product->getName()));

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/modifier/{cartItemId}', name: 'app_cart_update', methods: ['POST'], requirements: ['cartItemId' => '\d+'])]
    public function update(int $cartItemId, Request $request): Response
    {
        $cartItem = $this->cartItemRepository->find($cartItemId);
        if (!$cartItem) {
            throw $this->createNotFoundException();
        }

        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $this->em->remove($cartItem);
        } else {
            $cartItem->setQuantity($quantity);
        }

        $this->em->flush();
        $this->addFlash('success', 'Quantité mise à jour.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/supprimer/{cartItemId}', name: 'app_cart_remove', methods: ['POST'], requirements: ['cartItemId' => '\d+'])]
    public function remove(int $cartItemId): Response
    {
        $cartItem = $this->cartItemRepository->find($cartItemId);
        if ($cartItem) {
            $this->em->remove($cartItem);
            $this->em->flush();
            $this->addFlash('success', 'Article retiré du panier.');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/vider', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(): Response
    {
        $cart = $this->getOrCreateCart();
        foreach ($cart->getCartItems() as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('app_cart');
    }
}
