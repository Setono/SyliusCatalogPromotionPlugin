<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\QueryBuilder\Rule;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

class ContainsProductsRuleQueryBuilder implements RuleQueryBuilderInterface
{
    public const PARAMETER = 'products';

    /**
     * @throws StringsException
     */
    public function addRulesWheres(QueryBuilder $queryBuilder, array $configuration, string $alias): QueryBuilder
    {
        static $index = 0;

        ++$index;

        return $queryBuilder
            ->andWhere(sprintf('%s.code IN (:%s_%s)', $alias, self::PARAMETER, $index))
            ->setParameter(sprintf('%s_%s', self::PARAMETER, $index), $configuration[self::PARAMETER])
            ;
    }
}
