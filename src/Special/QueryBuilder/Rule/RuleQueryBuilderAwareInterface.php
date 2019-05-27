<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

interface RuleQueryBuilderAwareInterface
{
    public function setRuleQueryBuilder(RuleQueryBuilderServiceRegistry $ruleQueryBuilders): void;
}
