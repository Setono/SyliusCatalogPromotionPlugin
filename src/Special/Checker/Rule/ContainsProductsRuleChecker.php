<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Exception\UnsupportedTypeException;
use Sylius\Component\Core\Model\ProductInterface;

final class ContainsProductsRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'contains_products';

    /**
     * {@inheritdoc}
     */
    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool
    {
        if (!isset($configuration['products'])) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnsupportedTypeException($subject, ProductInterface::class);
        }

        if (\in_array($subject->getCode(), $configuration['products'], true)) {
            return true;
        }

        return false;
    }
}
