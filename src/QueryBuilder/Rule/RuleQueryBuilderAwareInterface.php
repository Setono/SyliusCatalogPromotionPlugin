<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule;

use Setono\SyliusBulkSpecialsPlugin\Registry\QueryBuilderRuleServiceRegistry;

// todo do we need this class?
interface RuleQueryBuilderAwareInterface
{
    public function setRuleQueryBuilder(QueryBuilderRuleServiceRegistry $ruleQueryBuilders): void;
}
