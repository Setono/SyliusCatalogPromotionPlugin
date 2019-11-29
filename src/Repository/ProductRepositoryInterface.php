<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Repository;

use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
}
