<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Application\Doctrine\ORM;

use Setono\SyliusCatalogPromotionPlugin\Doctrine\ORM\ProductRepositoryTrait;
use Setono\SyliusCatalogPromotionPlugin\Repository\ProductRepositoryInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    use ProductRepositoryTrait;
}
