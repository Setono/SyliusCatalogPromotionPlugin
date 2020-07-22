<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use function Safe\sprintf;

final class HasTaxonRule extends Rule
{
    public const TYPE = 'has_taxon';

    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('taxons', $configuration);
        $rootAlias = $this->getRootAlias($queryBuilder);
        $productAlias = self::generateAlias('product');
        $productTaxonsAlias = self::generateAlias('product_taxons');
        $taxonAlias = self::generateAlias('taxon');
        $parameter = self::generateParameter('taxon_codes');

        $queryBuilder
            ->join(sprintf('%s.product', $rootAlias), $productAlias)
            ->join(sprintf('%s.productTaxons', $productAlias), $productTaxonsAlias)
            ->join(sprintf('%s.taxon', $productTaxonsAlias), $taxonAlias)
            ->andWhere(sprintf(
                '%s.code IN (:%s)',
                $taxonAlias, $parameter
            ))
            ->setParameter($parameter, $value)
        ;
    }
}
