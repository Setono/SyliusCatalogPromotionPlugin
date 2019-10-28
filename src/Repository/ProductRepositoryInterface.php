<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Repository;

use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
}
