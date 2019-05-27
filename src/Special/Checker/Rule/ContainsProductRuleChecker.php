<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Exception\UnsupportedTypeException;
use Sylius\Component\Core\Model\ProductInterface;

final class ContainsProductRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'contains_product';

    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool
    {
        if (!isset($configuration['product'])) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnsupportedTypeException($subject, ProductInterface::class);
        }

        return $configuration['product'] === $subject->getCode();
    }
}
