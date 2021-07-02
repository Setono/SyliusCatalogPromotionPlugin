<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Repository;

use DateTimeInterface;

interface ChannelPricingRepositoryInterface extends HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * This resets the multiplier on all channel pricings
     *
     * @return int the number of updated rows in total
     */
    public function resetMultiplier(DateTimeInterface $dateTime): int;

    /**
     * @param bool $exclusive If true this method will overwrite the multiplier instead of multiplying it
     *
     * @return int the number of updated rows in total
     */
    public function updateMultiplier(
        float $multiplier,
        array $productVariantIds,
        array $channelCodes,
        DateTimeInterface $dateTime,
        string $bulkIdentifier,
        bool $exclusive = false,
        bool $manuallyDiscountedProductsExcluded = true
    ): int;

    /**
     * This method will update ALL channel prices in the given bulk
     *
     * @return int the number of updated rows in total
     */
    public function updatePrices(string $bulkIdentifier): int;
}
