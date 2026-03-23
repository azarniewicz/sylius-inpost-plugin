<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Model;

use Azarniewicz\SyliusInPostPlugin\Entity\InPostPointInterface;

interface InPostPointsAwareInterface
{
    public function getPoint(): ?InPostPointInterface;

    public function setPoint(?InPostPointInterface $point): void;
}
