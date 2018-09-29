<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRule;

/**
 * Class ProductRepository
 */
trait ProductRepositoryTrait
{
    /**
     * @param SpecialInterface $special
     *
     * @return array
     */
    public function findBySpecial(SpecialInterface $special): array
    {
        return $this->findBySpecialQB($special)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param SpecialInterface $special
     *
     * @return array
     */
    public function findBySpecialQB(SpecialInterface $special, $alias = 'product'): QueryBuilder
    {
        return $this->addRulesWheres($this->createQueryBuilder($alias), $special, $alias)
            ->distinct()
            ->join(sprintf('%s.specials', $alias), 'special')
            ->andWhere('special = :special')
            ->setParameter('special', $special)
            ->addOrderBy(sprintf('%s.id', $alias), 'ASC')
            ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param SpecialInterface $special
     *
     * @return QueryBuilder
     */
    protected function addRulesWheres(QueryBuilder $queryBuilder, SpecialInterface $special, $alias = 'product'): QueryBuilder
    {
        /** @var SpecialRule $rule */
        foreach ($special->getRules() as $index => $rule) {
            switch ($rule->getType()) {
                case 'contains_product':
                    return $queryBuilder
                        ->where(sprintf('%s.code IN (:productCodes_%s)', $alias, $index))
                        ->setParameter(sprintf('productCodes_%s', $index), $rule->getConfiguration()['product_code'])
                        ;
                case 'has_taxon':
                    return $queryBuilder
                        ->join(sprintf('%s.mainTaxon', $alias), 'mainTaxon')
                        ->join(sprintf('%s.productTaxons', $alias), 'pt')
                        ->join('pt.taxon', 'productTaxon')
                        ->where(sprintf('(mainTaxon.code IN (:taxonCodes_%s)) OR (productTaxon.code IN (:taxonCodes_%s))', $index, $index))
                        ->setParameter(sprintf('taxonCodes_%s', $index), $rule->getConfiguration()['taxons'])
                        ;
                default:
                    throw new \Exception(sprintf(
                        "Uknown rule type '%s'",
                        $rule->getType()
                    ));
            }
        }

        return $queryBuilder;
    }
}
