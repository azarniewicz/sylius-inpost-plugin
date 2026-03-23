<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Entity;

class InPostPoint implements InPostPointInterface
{
    protected ?int $id = null;

    private ?string $name = null;

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
}
