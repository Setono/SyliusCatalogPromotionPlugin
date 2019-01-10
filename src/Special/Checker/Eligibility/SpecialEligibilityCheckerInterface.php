<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

interface SpecialEligibilityCheckerInterface
{
    /**
     * @param SpecialSubjectInterface $specialSubject
     * @param SpecialInterface $special
     *
     * @return bool
     */
    public function isEligible(SpecialSubjectInterface $specialSubject, SpecialInterface $special): bool;
}
