<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Exception\UnsupportedTypeException;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Class ContainsProductRuleChecker
 */
final class ContainsProductRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'contains_product';

    /**
     * {@inheritdoc}
     */
    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool
    {
        if (!isset($configuration['product_code'])) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnsupportedTypeException($subject, ProductInterface::class);
        }

        if ($configuration['product_code'] === $subject->getCode()) {
            return true;
        }

        return false;
    }
}
