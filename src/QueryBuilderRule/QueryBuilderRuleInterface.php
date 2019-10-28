<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\QueryBuilderRule;

use Doctrine\ORM\QueryBuilder;

interface QueryBuilderRuleInterface
{
    public function filter(QueryBuilder $queryBuilder, array $configuration): void;
}
