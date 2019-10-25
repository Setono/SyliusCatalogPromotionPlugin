<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule\QueryBuilderRuleInterface;
use Setono\SyliusBulkSpecialsPlugin\Registry\QueryBuilderRuleServiceRegistry;

trait ProductRepositoryTrait
{
    /** @var QueryBuilderRuleServiceRegistry */
    protected $ruleQueryBuilders;

    /**
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    public function setRuleQueryBuilder(QueryBuilderRuleServiceRegistry $ruleQueryBuilders): void
    {
        $this->ruleQueryBuilders = $ruleQueryBuilders;
    }

    /**
     * Find Products, assigned to given Special
     *
     * @throws StringsException
     */
    public function findAssignedBySpecial(SpecialInterface $special): array
    {
        $alias = 'product';

        return $this->createQueryBuilder($alias)
            ->join(sprintf('%s.specials', $alias), 'special')
            ->andWhere('special = :special')
            ->setParameter('special', $special)
            ->addOrderBy(sprintf('%s.id', $alias), 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all Products that match given Special's Rules
     *
     * @throws StringsException
     */
    public function findBySpecial(SpecialInterface $special): array
    {
        return $this->findBySpecialQueryBuilder($special)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws StringsException
     */
    public function findBySpecialQueryBuilder(SpecialInterface $special): QueryBuilder
    {
        $alias = 'product';

        return $this->addRulesWheres($this->createQueryBuilder($alias), $special, $alias)
            ->distinct()
            ->addOrderBy(sprintf('%s.id', $alias), 'ASC')
        ;
    }

    protected function addRulesWheres(QueryBuilder $queryBuilder, SpecialInterface $special, string $alias): QueryBuilder
    {
        /** @var SpecialRuleInterface $rule */
        foreach ($special->getRules() as $rule) {
            /** @var QueryBuilderRuleInterface $ruleQueryBuilder */
            $ruleQueryBuilder = $this->ruleQueryBuilders->get($rule->getType());
            $ruleQueryBuilder->addRulesWheres(
                $queryBuilder,
                $rule->getConfiguration(),
                $alias
            );
        }

        return $queryBuilder;
    }
}
