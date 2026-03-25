<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface InPostPointInterface extends ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getAddressLine1(): ?string;

    public function setAddressLine1(?string $addressLine1): void;

    public function getAddressLine2(): ?string;

    public function setAddressLine2(?string $addressLine2): void;

    public function getLocationDescription(): ?string;

    public function setLocationDescription(?string $locationDescription): void;
}
