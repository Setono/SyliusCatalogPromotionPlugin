<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Component\Core\Model\Product;

/**
 * Interface SpecialEligibilityCheckerInterface
 */
interface SpecialEligibilityCheckerInterface
{
    /**
     * @param SpecialSubjectInterface|Product $specialSubject
     * @param SpecialInterface $special
     * @return bool
     */
    public function isEligible(SpecialSubjectInterface $specialSubject, SpecialInterface $special): bool;
}
