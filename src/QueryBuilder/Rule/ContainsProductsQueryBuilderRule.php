<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

class ContainsProductsQueryBuilderRule extends QueryBuilderRule
{
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
