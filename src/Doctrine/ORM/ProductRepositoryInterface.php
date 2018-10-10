<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

/**
 * Interface ProductRepositoryInterface
 */
interface ProductRepositoryInterface extends SpecialSubjectRepositoryInterface, BaseProductRepositoryInterface
{
}
