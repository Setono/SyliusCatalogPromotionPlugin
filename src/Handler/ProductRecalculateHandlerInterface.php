<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;

interface ProductRecalculateHandlerInterface extends HandlerInterface
{
    public function handleProduct(ProductInterface $product): void;
}
