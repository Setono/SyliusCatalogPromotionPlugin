<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;

interface ChannelPricingRepositoryInterface
{
    /**
     * This could reset the multiplier on ALL channel pricing
     */
    public function resetMultiplier(): void;

    /**
     * @param bool $exclusive If true this method will overwrite the multiplier instead of multiplying it
     */
    public function updateMultiplier(
        float $multiplier,
        QueryBuilder $productVariantQueryBuilder,
        array $channelCodes,
        bool $exclusive = false
    ): void;

    /**
     * This method will update ALL channel prices based on the multiplier
     */
    public function updatePrices(): void;
}
