<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ShippingMethodChecker
{
    public function isInPost(OrderInterface $order): bool
    {
        return $order->getShipments()->exists(
            fn(int $index, ShipmentInterface $shipment): bool =>
                $shipment->getMethod()?->getCode() === 'inpost_point'
        );
    }
}
