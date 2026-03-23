<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class HasPhoneNumberInPostOrder extends Constraint
{
    public string $message = 'azarniewicz_sylius_inpost_plugin.order.phone_number_required';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return HasPhoneNumberInPostOrderValidator::class;
    }
}
