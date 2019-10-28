<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\QueryBuilderRule;

use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

abstract class QueryBuilderRule implements QueryBuilderRuleInterface
{
    /** @var int */
    private static $aliasIndex = 0;

    /** @var int */
    private static $parameterIndex = 0;

    protected function getRootAlias(QueryBuilder $queryBuilder): string
    {
        $rootAliases = $queryBuilder->getRootAliases();

        if (count($rootAliases) === 0) {
            throw new RuntimeException('No root aliases');
        }

        return $rootAliases[0];
    }

    /**
     * @return mixed|null
     *
     * @throws StringsException
     */
    protected static function getConfigurationValue(string $key, array $configuration, bool $optional = false)
    {
        if (!$optional && !array_key_exists($key, $configuration)) {
            throw new InvalidArgumentException(sprintf('The key "%s" does not exist in the configuration', $key));
        }

        return $configuration[$key] ?? null;
    }

    protected static function generateAlias(string $prefix): string
    {
        $alias = $prefix . self::$aliasIndex;

        ++self::$aliasIndex;

        return $alias;
    }

    protected static function generateParameter(string $prefix): string
    {
        $parameter = $prefix . self::$parameterIndex;

        ++self::$parameterIndex;

        return $parameter;
    }
}
