<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'listProducts',cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $entityOrder = null;

    #[ORM\ManyToOne(inversedBy: 'listOrders',cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $entityProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEntityOrder(): ?Order
    {
        return $this->entityOrder;
    }

    public function setEntityOrder(?Order $entityOrder): self
    {
        $this->entityOrder = $entityOrder;

        return $this;
    }

    public function getEntityProduct(): ?Product
    {
        return $this->entityProduct;
    }

    public function setEntityProduct(?Product $entityProduct): self
    {
        $this->entityProduct = $entityProduct;
        return $this;
    }
}
