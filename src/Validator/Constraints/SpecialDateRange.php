<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class SpecialDateRange extends Constraint
{
    /**
     * @var string
     */
    public $message = 'setono_sylius_bulk_specials.special.end_date_cannot_be_set_prior_start_date';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'setono_sylius_bulk_specials_special_date_range_validator';
    }
}
