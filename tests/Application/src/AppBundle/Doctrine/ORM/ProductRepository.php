<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\ORM;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryTrait;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

/**
 * Class ProductRepository
 */
class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    use ProductRepositoryTrait;
}
