<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility;

use DateTime;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

final class SpecialDurationEligibilityChecker implements SpecialEligibilityCheckerInterface
{
    public function isEligible(SpecialSubjectInterface $specialSubject, SpecialInterface $special): bool
    {
        $now = new DateTime();

        $startsAt = $special->getStartsAt();
        if (null !== $startsAt && $now < $startsAt) {
            return false;
        }

        $endsAt = $special->getEndsAt();

        return !(null !== $endsAt && $now > $endsAt);
    }
}
