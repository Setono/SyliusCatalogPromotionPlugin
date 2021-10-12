<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Factory;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface PromotionRuleFactoryInterface extends FactoryInterface
{
    public function createByType(string $type, array $configuration, bool $strict = false): PromotionRuleInterface;

    public function createHasTaxon(array $taxonCodes): PromotionRuleInterface;

    public function createHasNotTaxon(array $taxonCodes): PromotionRuleInterface;

    public function createContainsProduct(string $productCode): PromotionRuleInterface;

    public function createContainsProducts(array $productCodes): PromotionRuleInterface;
}
