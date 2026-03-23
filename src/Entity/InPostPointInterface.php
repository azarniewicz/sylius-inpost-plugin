<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface InPostPointInterface extends ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;
}
