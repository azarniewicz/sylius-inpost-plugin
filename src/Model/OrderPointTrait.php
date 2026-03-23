<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Model;

use Azarniewicz\SyliusInPostPlugin\Entity\InPostPointInterface;

trait OrderPointTrait
{
    protected ?InPostPointInterface $point = null;

    public function getPoint(): ?InPostPointInterface
    {
        return $this->point;
    }

    public function setPoint(?InPostPointInterface $point): void
    {
        $this->point = $point;
    }
}
