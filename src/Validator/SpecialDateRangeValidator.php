<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Validator;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Validator\Constraints\SpecialDateRange;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class SpecialDateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var SpecialInterface $value */
        Assert::isInstanceOf($value, SpecialInterface::class);

        /** @var SpecialDateRange $constraint */
        Assert::isInstanceOf($constraint, SpecialDateRange::class);

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
