<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Rule;

use Doctrine\ORM\QueryBuilder;

interface RuleInterface
{
    public function filter(QueryBuilder $queryBuilder, array $configuration): void;
}
