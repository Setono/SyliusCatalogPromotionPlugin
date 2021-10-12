<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use function sprintf;
use Webmozart\Assert\Assert;

final class HasNotTaxonRule extends Rule
{
    public const TYPE = 'has_not_taxon';

    public function filter(QueryBuilder $queryBuilder, array $configuration): void
    {
        $value = self::getConfigurationValue('taxons', $configuration);
        Assert::isArray($value);

        $rootAlias = $this->getRootAlias($queryBuilder);
        $productAlias = $this->join($queryBuilder, sprintf('%s.product', $rootAlias), 'product');
        $productTaxonsAlias = $this->join($queryBuilder, sprintf('%s.productTaxons', $productAlias), 'product_taxons');
        $taxonAlias = $this->join($queryBuilder, sprintf('%s.taxon', $productTaxonsAlias), 'taxon');
        $parameter = self::generateParameter('taxon_codes');
        $queryBuilder
            ->andWhere(sprintf(
                '%s.code NOT IN (:%s)',
                $taxonAlias,
                $parameter
            ))
            ->setParameter($parameter, $value)
        ;
    }
}
