<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;

class ContainsProductsRuleQueryBuilder implements RuleQueryBuilderInterface
{
    const PARAMETER = 'product_codes';

    /**
     * {@inheritdoc}
     */
    public function addRulesWheres(QueryBuilder $queryBuilder, array $configuration, string $alias): QueryBuilder
    {
        static $index = 0;

        ++$index;

        return $queryBuilder
            ->andWhere(sprintf('%s.code IN (:%s_%s)', $alias, self::PARAMETER, $index))
            ->setParameter(sprintf('%s_%s', self::PARAMETER, $index), $configuration[self::PARAMETER])
            ;
    }
}
