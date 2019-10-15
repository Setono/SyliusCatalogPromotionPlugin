<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface SpecialRuleFactoryInterface extends FactoryInterface
{
    /**
     * @param array|string|mixed $configuration
     */
    public function createByType(string $type, $configuration): SpecialRuleInterface;

    public function createHasTaxon(array $taxons): SpecialRuleInterface;

    public function createContainsProduct(string $productCode): SpecialRuleInterface;

    public function createContainsProducts(array $productCodes): SpecialRuleInterface;
}
