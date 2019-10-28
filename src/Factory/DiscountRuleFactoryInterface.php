<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Factory;

use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface DiscountRuleFactoryInterface extends FactoryInterface
{
    /**
     * @param array|string|mixed $configuration
     */
    public function createByType(string $type, $configuration): DiscountRuleInterface;

    public function createHasTaxon(array $taxons): DiscountRuleInterface;

    public function createContainsProduct(string $productCode): DiscountRuleInterface;

    public function createContainsProducts(array $productCodes): DiscountRuleInterface;
}
