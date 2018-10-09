<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Interface SpecialSubjectRepositoryInterface
 */
interface SpecialSubjectRepositoryInterface
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
     *
     * @return QueryBuilder
     */
    public function findBySpecialQB(SpecialInterface $special): QueryBuilder;
}
