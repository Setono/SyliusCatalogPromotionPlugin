<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Rule;

use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use RuntimeException;
use function sprintf;
use Webmozart\Assert\Assert;

abstract class Rule implements RuleInterface
{
    private static int $aliasIndex = 0;

    private static int $parameterIndex = 0;

    protected function getRootAlias(QueryBuilder $queryBuilder): string
    {
        $rootAliases = $queryBuilder->getRootAliases();

        if (count($rootAliases) === 0) {
            throw new RuntimeException('No root aliases');
        }

        $rootAlias = $rootAliases[0];
        Assert::string($rootAlias);

        return $rootAlias;
    }

    /**
     * @return mixed|null
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
