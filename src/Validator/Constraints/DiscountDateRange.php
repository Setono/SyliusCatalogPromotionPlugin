<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class DiscountDateRange extends Constraint
{
    /** @var string */
    public $message = 'setono_sylius_bulk_discount.discount.end_date_cannot_be_set_prior_start_date';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'setono_sylius_bulk_discount_discount_date_range_validator';
    }
}
