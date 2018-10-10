<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Exception\UnsupportedTypeException;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Class ContainsProductRuleChecker
 */
final class ContainsProductsRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'contains_products';

    /**
     * {@inheritdoc}
     */
    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool
    {
        if (!isset($configuration['product_codes'])) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnsupportedTypeException($subject, ProductInterface::class);
        }

        if (in_array($subject->getCode(), $configuration['product_codes'])) {
            return true;
        }

        return false;
    }
}
