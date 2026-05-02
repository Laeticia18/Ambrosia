<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    private ?string $extraPrice = '0.00';

    #[ORM\Column]
    private bool $isPaid = false;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'options')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getExtraPrice(): ?string { return $this->extraPrice; }
    public function setExtraPrice(string $extraPrice): static { $this->extraPrice = $extraPrice; return $this; }

    public function isPaid(): bool { return $this->isPaid; }
    public function setIsPaid(bool $isPaid): static { $this->isPaid = $isPaid; return $this; }

    public function getProducts(): Collection { return $this->products; }
    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addOption($this);
        }
        return $this;
    }
    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeOption($this);
        }
        return $this;
    }
}
