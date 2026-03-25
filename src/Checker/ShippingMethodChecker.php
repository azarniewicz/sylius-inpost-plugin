<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ShippingMethodChecker
{
    public function __construct(
        private readonly string $shippingMethodCode,
    ) {
    }

    public function isInPost(OrderInterface $order): bool
    {
        return $order->getShipments()->exists(
            fn(int $index, ShipmentInterface $shipment): bool =>
                $shipment->getMethod()?->getCode() === $this->shippingMethodCode
        );
    }
}
