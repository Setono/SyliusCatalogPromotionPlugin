<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Validator\Constraints;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionDateRangeValidator extends ConstraintValidator
{
    /**
     * @param PromotionInterface|mixed $value
     * @param PromotionDateRange|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        Assert::isInstanceOf($value, PromotionInterface::class);
        Assert::isInstanceOf($constraint, PromotionDateRange::class);

        $startsAt = $value->getStartsAt();
        $endsAt = $value->getEndsAt();

        if (null === $startsAt || null === $endsAt) {
            return;
        }

        if ($startsAt->getTimestamp() > $endsAt->getTimestamp()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('endsAt')
                ->addViolation()
            ;
        }
    }
}
