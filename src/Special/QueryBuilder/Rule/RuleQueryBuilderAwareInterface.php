<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

/**
 * Interface RuleQueryBuilderAwareInterface
 */
interface RuleQueryBuilderAwareInterface
{
    /**
     * @param RuleQueryBuilderServiceRegistry $ruleQueryBuilders
     */
    public function setRuleQueryBuilder(RuleQueryBuilderServiceRegistry $ruleQueryBuilders): void;
}
