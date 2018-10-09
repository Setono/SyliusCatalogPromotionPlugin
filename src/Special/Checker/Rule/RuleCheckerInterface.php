<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

/**
 * Interface RuleCheckerInterface
 */
interface RuleCheckerInterface
{
    /**
     * @param SpecialSubjectInterface $subject
     * @param array $configuration
     *
     * @return bool
     */
    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool;
}
