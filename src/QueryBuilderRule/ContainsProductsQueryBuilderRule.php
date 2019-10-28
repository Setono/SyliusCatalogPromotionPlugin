<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\QueryBuilderRule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class ContainsProductsQueryBuilderRule extends QueryBuilderRule
{
    public const TYPE = 'contains_products';

    /**
     * @throws StringsException
     */
    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('products', $configuration);
        $alias = $this->getRootAlias($queryBuilder);
        $parameter = self::generateParameter('product_codes');

        $queryBuilder
            ->join(sprintf('%s.product', $alias), self::generateAlias('product'))
            ->andWhere(sprintf('%s.code IN (:%s)', $alias, $parameter))
            ->setParameter($parameter, $value)
        ;
    }
}
