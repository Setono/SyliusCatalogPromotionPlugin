<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContainsProductRule extends Rule
{
    public const TYPE = 'contains_product';

    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('product', $configuration);
        Assert::string($value);

        $rootAlias = $this->getRootAlias($queryBuilder);
        $productAlias = self::generateAlias('product');
        $parameter = self::generateParameter('product_code');

        $queryBuilder
            ->join(sprintf('%s.product', $rootAlias), $productAlias)
            ->andWhere(sprintf('%s.code = :%s', $productAlias, $parameter))
            ->setParameter($parameter, $value)
        ;
    }
}
