<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Validator\Constraints;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionRuleInterface;
use Setono\SyliusCatalogPromotionPlugin\Rule\ContainsProductRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\ContainsProductsRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\HasTaxonRule;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionRulesNotEmptyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, PromotionRuleInterface::class);

        if (HasTaxonRule::TYPE === $value->getType() && empty($value->getConfiguration()['taxons'])) {

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('taxons')
                ->addViolation()
            ;

            return;
        }

        if (ContainsProductsRule::TYPE === $value->getType() && empty($value->getConfiguration()['products'])) {

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('products')
                ->addViolation()
            ;

            return;
        }

        if (ContainsProductRule::TYPE === $value->getType() && empty($value->getConfiguration()['product'])) {

            $this->context
                ->buildViolation($constraint->message)
                ->atPath('product')
                ->addViolation()
            ;
        }
    }
}
