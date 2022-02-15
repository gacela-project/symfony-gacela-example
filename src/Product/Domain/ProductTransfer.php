<?php

declare(strict_types=1);

namespace App\Product\Domain;

use App\Product\Infrastructure\Persistence\Product;

final class ProductTransfer
{
    private ?int $id = null;

    private ?string $name = null;

    private ?int $price = null;

    public static function fromProductEntity(Product $entity): self
    {
        $self = new self();
        $self->id = $entity->getId();
        $self->name = $entity->getName();
        $self->price = $entity->getPrice();

        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;
        return $this;
    }
}
