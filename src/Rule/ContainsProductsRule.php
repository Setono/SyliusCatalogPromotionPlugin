<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContainsProductsRule extends Rule
{
    public const TYPE = 'contains_products';

    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('products', $configuration);
        Assert::string($value);

        $rootAlias = $this->getRootAlias($queryBuilder);
        $productAlias = self::generateAlias('product');
        $parameter = self::generateParameter('product_codes');

        $queryBuilder
            ->join(sprintf('%s.product', $rootAlias), $productAlias)
            ->andWhere(sprintf('%s.code IN (:%s)', $productAlias, $parameter))
            ->setParameter($parameter, $value)
        ;
    }
}
