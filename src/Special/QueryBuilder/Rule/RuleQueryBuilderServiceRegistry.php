<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Sylius\Component\Registry\ServiceRegistry;

/**
 * Class RuleQueryBuilderServiceRegistry
 */
class RuleQueryBuilderServiceRegistry extends ServiceRegistry
{
    /**
     * RuleQueryBuilderServiceRegistry constructor.
     */
    public function __construct()
    {
        parent::__construct(
            RuleQueryBuilderInterface::class,
            'rule query builder'
        );
    }

    /**
     * @return RuleQueryBuilderInterface[]
     */
    public function all(): array
    {
        return parent::all();
    }

    /**
     * @param string $identifier
     * @return RuleQueryBuilderInterface
     */
    public function get(string $identifier)
    {
        return parent::get($identifier);
    }
}