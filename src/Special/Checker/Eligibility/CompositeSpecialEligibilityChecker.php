<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Webmozart\Assert\Assert;

final class CompositeSpecialEligibilityChecker implements SpecialEligibilityCheckerInterface
{
    /** @var SpecialEligibilityCheckerInterface[] */
    private $specialEligibilityCheckers;

    /**
     * @param SpecialEligibilityCheckerInterface[]|mixed[] $specialEligibilityCheckers
     */
    public function __construct(array $specialEligibilityCheckers)
    {
        Assert::notEmpty($specialEligibilityCheckers);
        Assert::allIsInstanceOf($specialEligibilityCheckers, SpecialEligibilityCheckerInterface::class);

        $this->specialEligibilityCheckers = $specialEligibilityCheckers;
    }

    public function isEligible(SpecialSubjectInterface $specialSubject, SpecialInterface $special): bool
    {
        foreach ($this->specialEligibilityCheckers as $specialEligibilityChecker) {
            if (!$specialEligibilityChecker->isEligible($specialSubject, $special)) {
                return false;
            }
        }

        return true;
    }
}
