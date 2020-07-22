<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class ManuallyDiscountedProductsExcludedRule extends Rule
{
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
