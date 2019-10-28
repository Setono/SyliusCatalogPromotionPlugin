<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Validator;

use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Validator\Constraints\DiscountDateRange;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class DiscountDateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var DiscountInterface $value */
        Assert::isInstanceOf($value, DiscountInterface::class);

        /** @var DiscountDateRange $constraint */
        Assert::isInstanceOf($constraint, DiscountDateRange::class);

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
