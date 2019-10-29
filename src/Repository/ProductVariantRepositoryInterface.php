<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Repository;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;

interface ProductVariantRepositoryInterface extends BaseProductVariantRepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
}
