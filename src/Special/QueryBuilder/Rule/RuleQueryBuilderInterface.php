<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;

interface RuleQueryBuilderInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array $configuration
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function addRulesWheres(QueryBuilder $queryBuilder, array $configuration, string $alias): QueryBuilder;
}
