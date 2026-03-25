<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Entity;

class InPostPoint implements InPostPointInterface
{
    protected ?int $id = null;

    private ?string $name = null;

    private ?string $addressLine1 = null;

    private ?string $addressLine2 = null;

    private ?string $locationDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): void
    {
        $this->addressLine1 = $addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): void
    {
        $this->addressLine2 = $addressLine2;
    }

    public function getLocationDescription(): ?string
    {
        return $this->locationDescription;
    }

    public function setLocationDescription(?string $locationDescription): void
    {
        $this->locationDescription = $locationDescription;
    }
}
