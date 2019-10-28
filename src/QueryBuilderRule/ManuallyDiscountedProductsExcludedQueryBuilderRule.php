<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class ManuallyDiscountedProductsExcludedQueryBuilderRule extends QueryBuilderRule
{
    /**
     * @throws StringsException
     */
    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $rootAlias = $this->getRootAlias($queryBuilder);
        $channelPricingsAlias = self::generateAlias('channelPricings');

        $queryBuilder
            ->join(sprintf('%s.channelPricings', $rootAlias), $channelPricingsAlias)
            ->andWhere(sprintf('%s.manuallyDiscounted = false', $channelPricingsAlias))
        ;
    }
}
