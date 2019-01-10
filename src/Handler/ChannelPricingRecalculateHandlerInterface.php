<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Sylius\Component\Core\Model\ChannelPricingInterface;

interface ChannelPricingRecalculateHandlerInterface extends HandlerInterface
{
    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function handleChannelPricing(ChannelPricingInterface $channelPricing): void;
}
