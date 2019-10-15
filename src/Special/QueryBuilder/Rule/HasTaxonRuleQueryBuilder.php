<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

class HasTaxonRuleQueryBuilder implements RuleQueryBuilderInterface
{
    public const PARAMETER = 'taxons';

    /**
     * @throws StringsException
     */
    public function addRulesWheres(QueryBuilder $queryBuilder, array $configuration, string $alias): QueryBuilder
    {
        static $index = 0;

        ++$index;

        return $queryBuilder
            ->join(sprintf('%s.mainTaxon', $alias), 'mainTaxon')
            ->join(sprintf('%s.productTaxons', $alias), 'pt')
            ->join('pt.taxon', 'productTaxon')
            ->andWhere(sprintf('(mainTaxon.code IN (:%s_%s)) OR (productTaxon.code IN (:%s_%s))', self::PARAMETER, $index, self::PARAMETER, $index))
            ->setParameter(sprintf('%s_%s', self::PARAMETER, $index), $configuration[self::PARAMETER])
            ;
    }
}
