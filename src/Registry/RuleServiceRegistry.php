<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Registry;

use Setono\SyliusBulkDiscountPlugin\Rule\RuleInterface;
use Sylius\Component\Registry\ServiceRegistry;

class RuleServiceRegistry extends ServiceRegistry
{
    public function __construct()
    {
        parent::__construct(
            RuleInterface::class,
            'query builder rule'
        );
    }
}
