<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

interface RuleCheckerInterface
{
    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool;
}
