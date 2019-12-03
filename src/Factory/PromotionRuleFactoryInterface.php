<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Factory;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface PromotionRuleFactoryInterface extends FactoryInterface
{
    /**
     * @param array|string|mixed $configuration
     */
    public function createByType(string $type, $configuration): PromotionRuleInterface;

    public function createHasTaxon(array $taxonCodes): PromotionRuleInterface;

    public function createContainsProduct(string $productCode): PromotionRuleInterface;

    public function createContainsProducts(array $productCodes): PromotionRuleInterface;
}
