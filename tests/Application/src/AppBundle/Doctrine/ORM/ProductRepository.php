<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\ORM;

use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryTrait;
use Setono\SyliusBulkSpecialsPlugin\QueryBuilder\Rule\RuleQueryBuilderAwareInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface, RuleQueryBuilderAwareInterface
{
    use ProductRepositoryTrait;
}
