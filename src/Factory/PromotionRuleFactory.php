<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Factory;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionRuleInterface;
use Setono\SyliusCatalogPromotionsPlugin\Rule\ContainsProductRule;
use Setono\SyliusCatalogPromotionsPlugin\Rule\ContainsProductsRule;
use Setono\SyliusCatalogPromotionsPlugin\Rule\HasTaxonRule;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
     *
     * @throws StringsException
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

    public function createHasTaxon(array $taxons): PromotionRuleInterface
    {
        return $this->createPromotionRule(
            HasTaxonRule::TYPE,
            ['taxons' => $taxons]
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
