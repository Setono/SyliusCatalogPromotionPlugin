<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Eligibility;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class SpecialRulesEligibilityChecker implements SpecialEligibilityCheckerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $ruleRegistry;

    /**
     * @param ServiceRegistryInterface $ruleRegistry
     */
    public function __construct(ServiceRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    public function isEligible(SpecialSubjectInterface $specialSubject, SpecialInterface $special): bool
    {
        if (!$special->hasRules()) {
            return false;
        }

        foreach ($special->getRules() as $rule) {
            if (!$this->isEligibleToRule($specialSubject, $rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param SpecialSubjectInterface $specialSubject
     * @param SpecialRuleInterface $rule
     *
     * @return bool
     */
    private function isEligibleToRule(SpecialSubjectInterface $specialSubject, SpecialRuleInterface $rule): bool
    {
        /** @var RuleCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($rule->getType());

        return $checker->isEligible($specialSubject, $rule->getConfiguration());
    }
}
