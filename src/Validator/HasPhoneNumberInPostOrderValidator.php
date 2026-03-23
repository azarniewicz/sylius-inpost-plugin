<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Validator;

use Azarniewicz\SyliusInPostPlugin\Checker\ShippingMethodChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class HasPhoneNumberInPostOrderValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ShippingMethodChecker $shippingMethodChecker,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof OrderInterface) {
            return;
        }

        if (!$this->shippingMethodChecker->isInPost($value)) {
            return;
        }

        if ($this->validatePhoneNumber($value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }

    private function validatePhoneNumber(OrderInterface $order): bool
    {
        $shippingAddress = $order->getShippingAddress();

        if ($shippingAddress === null) {
            return false;
        }

        return $shippingAddress->getPhoneNumber() !== null;
    }
}
