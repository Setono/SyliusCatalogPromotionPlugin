<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class HasTaxonRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'has_taxon';

    public function isEligible(SpecialSubjectInterface $subject, array $configuration): bool
    {
        if (!isset($configuration['taxons'])) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnexpectedTypeException($subject, ProductInterface::class);
        }

        return $this->hasProductValidTaxon($subject, $configuration);
    }

    private function hasProductValidTaxon(ProductInterface $product, array $configuration): bool
    {
        foreach ($product->getTaxons() as $taxon) {
            if (\in_array($taxon->getCode(), $configuration['taxons'], true)) {
                return true;
            }
        }

        return false;
    }
}
