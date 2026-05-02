<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\ManyToMany(targetEntity: Option::class)]
    private Collection $selectedOptions;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $unitPrice = null;

    public function __construct()
    {
        $this->selectedOptions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getCart(): ?Cart { return $this->cart; }
    public function setCart(?Cart $cart): static { $this->cart = $cart; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): static { $this->product = $product; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getSelectedOptions(): Collection { return $this->selectedOptions; }
    public function addSelectedOption(Option $option): static
    {
        if (!$this->selectedOptions->contains($option)) {
            $this->selectedOptions->add($option);
        }
        return $this;
    }
    public function removeSelectedOption(Option $option): static
    {
        $this->selectedOptions->removeElement($option);
        return $this;
    }

    public function getUnitPrice(): ?string { return $this->unitPrice; }
    public function setUnitPrice(string $unitPrice): static { $this->unitPrice = $unitPrice; return $this; }

    public function getSubtotal(): float
    {
        return (float)$this->unitPrice * $this->quantity;
    }

    public function getOptionsKey(): string
    {
        $ids = $this->selectedOptions->map(fn($o) => $o->getId())->toArray();
        sort($ids);
        return implode(',', $ids);
    }
}
