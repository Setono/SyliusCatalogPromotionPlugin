<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Repository;

use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface DiscountRepositoryInterface extends RepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * This is the method used for processing of discounts
     * It should return discounts with these properties
     * - Enabled
     * - At least one enabled channel
     * - Sorted by exclusive ascending and thereafter priority
     * - The current time should be within the respective discounts time interval
     *
     * @return DiscountInterface[]
     */
    public function findForProcessing(): array;
}
