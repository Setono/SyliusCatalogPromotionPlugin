<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Factory;

use InvalidArgumentException;
use function Safe\sprintf;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionRuleInterface;
use Setono\SyliusCatalogPromotionPlugin\Rule\ContainsProductRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\ContainsProductsRule;
use Setono\SyliusCatalogPromotionPlugin\Rule\HasTaxonRule;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class PromotionRuleFactory implements PromotionRuleFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createNew(): PromotionRuleInterface
    {
        /** @var PromotionRuleInterface $obj */
        $obj = $this->decoratedFactory->createNew();

        return $obj;
    }

    /**
     * @param array|string|mixed $configuration
     */
    public function createByType(string $type, $configuration): PromotionRuleInterface
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

    public function createHasTaxon(array $taxonCodes): PromotionRuleInterface
    {
        Assert::allString($taxonCodes);

        return $this->createPromotionRule(
            HasTaxonRule::TYPE,
            ['taxons' => $taxonCodes]
        );
    }

    public function createContainsProduct(string $productCode): PromotionRuleInterface
    {
        return $this->createPromotionRule(
            ContainsProductRule::TYPE,
            ['product' => $productCode]
        );
    }

    public function createContainsProducts(array $productCodes): PromotionRuleInterface
    {
        Assert::allString($productCodes);

        return $this->createPromotionRule(
            ContainsProductsRule::TYPE,
            ['products' => $productCodes]
        );
    }

    private function createPromotionRule(string $type, array $configuration): PromotionRuleInterface
    {
        $rule = $this->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
