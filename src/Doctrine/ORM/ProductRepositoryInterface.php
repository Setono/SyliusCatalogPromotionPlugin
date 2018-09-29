<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Interface ProductRepositoryInterface
 */
interface ProductRepositoryInterface
{
    /**
     * @param SpecialInterface $special
     * @return array
     */
    public function findBySpecial(SpecialInterface $special): array;

    /**
     * Should be used to build some paginators
     *
     * @param SpecialInterface $special
     * @param string $alias
     * @return QueryBuilder
     */
    public function findBySpecialQB(SpecialInterface $special, $alias = 'product'): QueryBuilder;
}
