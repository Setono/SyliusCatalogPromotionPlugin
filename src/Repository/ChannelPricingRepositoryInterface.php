<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionsPlugin\Repository;

use DateTimeInterface;

interface ChannelPricingRepositoryInterface extends HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * This could reset the multiplier on ALL channel pricing
     */
    public function resetMultiplier(DateTimeInterface $dateTime): void;

    /**
     * @param bool $exclusive If true this method will overwrite the multiplier instead of multiplying it
     */
    public function updateMultiplier(
        float $multiplier,
        array $productVariantIds,
        array $channelCodes,
        DateTimeInterface $dateTime,
        bool $exclusive = false
    ): void;

    /**
     * This method will update ALL channel prices based on the multiplier and updated after $dateTime
     */
    public function updatePrices(DateTimeInterface $dateTime): void;
}
