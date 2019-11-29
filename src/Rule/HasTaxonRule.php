<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

final class HasTaxonRule extends Rule
{
    public const TYPE = 'has_taxon';

    /**
     * @throws StringsException
     */
    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('taxons', $configuration);
        $rootAlias = $this->getRootAlias($queryBuilder);
        $productAlias = self::generateAlias('product');
        $mainTaxonAlias = self::generateAlias('main_taxon');
        $productTaxonsAlias = self::generateAlias('product_taxons');
        $taxonAlias = self::generateAlias('taxon');
        $parameter = self::generateParameter('taxon_codes');

        $queryBuilder
            ->join(sprintf('%s.product', $rootAlias), $productAlias)
            ->join(sprintf('%s.mainTaxon', $productAlias), $mainTaxonAlias) // todo with these joins it says that we HAVE to have a main taxon, but that is not required
            ->join(sprintf('%s.productTaxons', $productAlias), $productTaxonsAlias)
            ->join(sprintf('%s.taxon', $productTaxonsAlias), $taxonAlias)
            ->andWhere(sprintf(
                '%s.code IN (:%s) OR %s.code IN (:%s)',
                $mainTaxonAlias, $parameter, $taxonAlias, $parameter
            ))
            ->setParameter($parameter, $value)
        ;
    }
}
