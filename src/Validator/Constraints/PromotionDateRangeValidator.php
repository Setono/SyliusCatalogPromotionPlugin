<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Validator\Constraints;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionDateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var PromotionInterface $value */
        Assert::isInstanceOf($value, PromotionInterface::class);

        /** @var PromotionDateRange $constraint */
        Assert::isInstanceOf($constraint, PromotionDateRange::class);

        if (null === $value->getStartsAt() || null === $value->getEndsAt()) {
            return;
        }

        if ($value->getStartsAt()->getTimestamp() > $value->getEndsAt()->getTimestamp()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('endsAt')
                ->addViolation()
            ;
        }
    }
}
