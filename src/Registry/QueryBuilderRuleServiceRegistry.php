<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Registry;

use Setono\SyliusBulkDiscountPlugin\QueryBuilderRule\QueryBuilderRuleInterface;
use Sylius\Component\Registry\ServiceRegistry;

class QueryBuilderRuleServiceRegistry extends ServiceRegistry
{
    public function __construct()
    {
        parent::__construct(
            QueryBuilderRuleInterface::class,
            'query builder rule'
        );
    }
}
