<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Factory;

use Azarniewicz\SyliusInPostPlugin\Entity\InPostPoint;
use Azarniewicz\SyliusInPostPlugin\Entity\InPostPointInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class InPostPointFactory implements FactoryInterface
{
    public function createNew(): InPostPointInterface
    {
        return new InPostPoint();
    }
}
