<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Factory;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Setono\SyliusBulkDiscountPlugin\Rule\ContainsProductRule;
use Setono\SyliusBulkDiscountPlugin\Rule\ContainsProductsRule;
use Setono\SyliusBulkDiscountPlugin\Rule\HasTaxonRule;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class DiscountRuleFactory implements DiscountRuleFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createNew(): DiscountRuleInterface
    {
        /** @var DiscountRuleInterface $obj */
        $obj = $this->decoratedFactory->createNew();

        return $obj;
    }

    /**
     * @param array|string|mixed $configuration
     *
     * @throws StringsException
     */
    public function createByType(string $type, $configuration): DiscountRuleInterface
    {
        switch ($type) {
            case HasTaxonRule::TYPE:
                return $this->createHasTaxon((array) $configuration);
            case ContainsProductRule::TYPE:
                if (is_array($configuration)) {
                    throw new InvalidArgumentException(
                        'The createContainsProduct method only accepts a string'
                    );
                }

                return $this->createContainsProduct((string) $configuration);
            case ContainsProductsRule::TYPE:
                return $this->createContainsProducts((array) $configuration);
        }

        throw new InvalidArgumentException(sprintf('type must be one of [%s]', implode(', ', [
            HasTaxonRule::TYPE,
            ContainsProductRule::TYPE,
            ContainsProductsRule::TYPE,
        ])));
    }

    public function createHasTaxon(array $taxons): DiscountRuleInterface
    {
        return $this->createDiscountRule(
            HasTaxonRule::TYPE,
            ['taxons' => $taxons]
        );
    }

    public function createContainsProduct(string $productCode): DiscountRuleInterface
    {
        return $this->createDiscountRule(
            ContainsProductRule::TYPE,
            ['product' => $productCode]
        );
    }

    public function createContainsProducts(array $productCodes): DiscountRuleInterface
    {
        return $this->createDiscountRule(
            ContainsProductsRule::TYPE,
            ['products' => $productCodes]
        );
    }

    private function createDiscountRule(string $type, array $configuration): DiscountRuleInterface
    {
        $rule = $this->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
