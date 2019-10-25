<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;

interface QueryBuilderRuleInterface
{
    public function filter(QueryBuilder $queryBuilder, array $configuration): void;
}
