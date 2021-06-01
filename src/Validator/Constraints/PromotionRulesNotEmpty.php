<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class PromotionRulesNotEmpty extends Constraint
{
    /** @var string */
    public $message = 'setono_sylius_catalog_promotion.promotion.rule_not_be_empty';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'setono_sylius_catalog_promotion_promotion_rules_not_empty_validator';
    }
}
