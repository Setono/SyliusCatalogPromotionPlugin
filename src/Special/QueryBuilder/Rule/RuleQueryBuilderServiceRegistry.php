<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Sylius\Component\Registry\ServiceRegistry;

class RuleQueryBuilderServiceRegistry extends ServiceRegistry
{
    public function __construct()
    {
        parent::__construct(
            RuleQueryBuilderInterface::class,
            'rule query builder'
        );
    }
}
