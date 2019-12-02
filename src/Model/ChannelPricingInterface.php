<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Model;

use Sylius\Component\Core\Model\ChannelPricingInterface as BaseChannelPricingInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ChannelPricingInterface extends BaseChannelPricingInterface, TimestampableInterface
{
    /**
     * @return bool Returns true if this was discounted manually
     */
    public function isManuallyDiscounted(): bool;

    public function setManuallyDiscounted(bool $manuallyDiscounted): void;

    public function getMultiplier(): float;
}
