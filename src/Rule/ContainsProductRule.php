<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class ContainsProductRule extends Rule
{
    public const TYPE = 'contains_product';

    /**
     * @throws StringsException
     */
    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('product', $configuration);
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
