<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Factory;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule\ContainsProductQueryBuilderRule;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule\ContainsProductsQueryBuilderRule;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule\HasTaxonQueryBuilderRule;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class SpecialRuleFactory implements SpecialRuleFactoryInterface
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
     * @param array|string|mixed $configuration
     *
     * @throws StringsException
     */
    public function createByType(string $type, $configuration): SpecialRuleInterface
    {
        switch ($type) {
            case HasTaxonQueryBuilderRule::TYPE:
                return $this->createHasTaxon((array) $configuration);
            case ContainsProductQueryBuilderRule::TYPE:
                if (is_array($configuration)) {
                    throw new InvalidArgumentException(
                        'The createContainsProduct method only accepts a string'
                    );
                }

                return $this->createContainsProduct((string) $configuration);
            case ContainsProductsQueryBuilderRule::TYPE:
                return $this->createContainsProducts((array) $configuration);
        }

        throw new InvalidArgumentException(sprintf('type must be one of [%s]', implode(', ', [
            HasTaxonQueryBuilderRule::TYPE,
            ContainsProductQueryBuilderRule::TYPE,
            ContainsProductsQueryBuilderRule::TYPE,
        ])));
    }

    public function createHasTaxon(array $taxons): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            HasTaxonQueryBuilderRule::TYPE,
            ['taxons' => $taxons]
        );
    }

    public function createContainsProduct(string $productCode): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            ContainsProductQueryBuilderRule::TYPE,
            ['product' => $productCode]
        );
    }

    public function createContainsProducts(array $productCodes): SpecialRuleInterface
    {
        return $this->createSpecialRule(
            ContainsProductsQueryBuilderRule::TYPE,
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
