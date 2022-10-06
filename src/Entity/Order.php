<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $delivery = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $entityUser = null;

    #[ORM\OneToMany(mappedBy: 'entityOrder', targetEntity: Detail::class, orphanRemoval: true)]
    private Collection $listProducts;

    #[ORM\Column(length: 255)]
    private ?string $deliverySpeed = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentMethods = null;

    public function __construct()
    {
        $this->listProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDelivery(): ?float
    {
        return $this->delivery;
    }

    public function setDelivery(float $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEntityUser(): ?User
    {
        return $this->entityUser;
    }

    public function setEntityUser(?User $entityUser): self
    {
        $this->entityUser = $entityUser;

        return $this;
    }

    /**
     * @return Collection<int, Detail>
     */
    public function getListProducts(): Collection
    {
        return $this->listProducts;
    }

    public function addListProduct(Detail $listProduct): self
    {
        if (!$this->listProducts->contains($listProduct)) {
            $this->listProducts->add($listProduct);
            $listProduct->setEntityOrder($this);
        }

        return $this;
    }

    public function removeListProduct(Detail $listProduct): self
    {
        if ($this->listProducts->removeElement($listProduct)) {
            // set the owning side to null (unless already changed)
            if ($listProduct->getEntityOrder() === $this) {
                $listProduct->setEntityOrder(null);
            }
        }

        return $this;
    }

    public function getDeliverySpeed(): ?string
    {
        return $this->deliverySpeed;
    }

    public function setDeliverySpeed(string $deliverySpeed): self
    {
        $this->deliverySpeed = $deliverySpeed;

        return $this;
    }

    public function getPaymentMethods(): ?string
    {
        return $this->paymentMethods;
    }

    public function setPaymentMethods(string $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;

        return $this;
    }
}
