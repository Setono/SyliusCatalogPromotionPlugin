<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Factory;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface SpecialRuleFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     * @param string|array $configuration
     *
     * @return SpecialRuleInterface
     */
    public function createByType(string $type, $configuration): SpecialRuleInterface;

    /**
     * @param array $taxons
     *
     * @return SpecialRuleInterface
     */
    public function createHasTaxon(array $taxons): SpecialRuleInterface;

    /**
     * @param string $productCode
     *
     * @return SpecialRuleInterface
     */
    public function createContainsProduct(string $productCode): SpecialRuleInterface;

    /**
     * @param array $productCodes
     *
     * @return SpecialRuleInterface
     */
    public function createContainsProducts(array $productCodes): SpecialRuleInterface;
}
