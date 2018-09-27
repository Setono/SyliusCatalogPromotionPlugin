<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialRule;

/**
 * Class ProductRepository
 */
trait ProductRepositoryTrait
{
    /**
     * @param Special $special
     *
     * @return array
     */
    public function findBySpecial(Special $special): array
    {
        return $this->findBySpecialQB($special)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param Special $special
     *
     * @return array
     */
    public function findBySpecialQB(Special $special): QueryBuilder
    {
        return $this->addRulesWheres($this->createQueryBuilder('product')
            ->where(':special IN p.specials')
            ->setParameter('special', $special)
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Special $special
     *
     * @return QueryBuilder
     */
    protected function addRulesWheres(QueryBuilder $queryBuilder, Special $special): QueryBuilder
    {
        /** @var SpecialRule $rule */
        foreach ($special->getRules() as $index => $rule) {
            switch ($rule->getType()) {
                case 'contains_product':
                    return $queryBuilder
                        ->orWhere(sprintf('(product.code IN :productCodes_%s)', $index))
                        ->setParameter(sprintf('productCodes_%s', $index), $rule->getConfiguration())
                        ;
                case 'has_taxon':
                    return $queryBuilder
                        ->join('product.mainTaxon', 'mainTaxon')
                        ->join('product.productTaxons', 'pt')
                        ->join('product.taxon', 'productTaxon')
                        ->orWhere(sprintf('(mainTaxon.code IN :taxonCodes_%s OR productTaxon.code IN :taxonCodes_%s)', $index))
                        ->setParameter(sprintf('taxonCodes_%s', $index), $rule->getConfiguration())
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
