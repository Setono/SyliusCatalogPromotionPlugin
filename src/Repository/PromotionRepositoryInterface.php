<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Repository;

use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PromotionRepositoryInterface extends RepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * This is the method used for processing of promotions
     * It should return promotions with these properties
     * - Enabled
     * - At least one enabled channel
     * - Sorted by exclusive ascending and thereafter priority
     * - The current time should be within the respective promotions time interval
     *
     * @return PromotionInterface[]
     */
    public function findForProcessing(): array;
}
