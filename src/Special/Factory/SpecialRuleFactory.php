<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Factory;

use InvalidArgumentException;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule\ContainsProductRuleChecker;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule\ContainsProductsRuleChecker;
use Setono\SyliusBulkSpecialsPlugin\Special\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Resource\Factory\FactoryInterface;

class SpecialRuleFactory implements SpecialRuleFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createNew(): SpecialRuleInterface
    {
        /** @var SpecialRuleInterface $obj */
        $obj = $this->decoratedFactory->createNew();

        return $obj;
    }

    /**
     * @todo Transform switch to ServiceRegistry
     *
     * {@inheritdoc}
     */
    public function createByType(string $type, $configuration): SpecialRuleInterface
    {
        switch ($type) {
            case HasTaxonRuleChecker::TYPE:
                return $this->createHasTaxon((array) $configuration);
            case ContainsProductRuleChecker::TYPE:
                if (is_array($configuration)) {
                    throw new InvalidArgumentException('The createContainsProduct method only accepts a string');
                }

                return $this->createContainsProduct((string) $configuration);
            case ContainsProductsRuleChecker::TYPE:
                return $this->createContainsProducts((array) $configuration);
        }

        throw new InvalidArgumentException('$type must be one of [' . HasTaxonRuleChecker::TYPE . ', ' . ContainsProductRuleChecker::TYPE . ', ' . ContainsProductsRuleChecker::TYPE . ']');
    }

    public function createHasTaxon(array $taxons): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            HasTaxonRuleChecker::TYPE,
            ['taxons' => $taxons]
        );
    }

    public function createContainsProduct(string $productCode): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            ContainsProductRuleChecker::TYPE,
            ['product' => $productCode]
        );
    }

    public function createContainsProducts(array $productCodes): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            ContainsProductsRuleChecker::TYPE,
            ['products' => $productCodes]
        );
    }

    private function createSpecialRule(string $type, array $configuration): SpecialRuleInterface
    {
        $rule = $this->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
