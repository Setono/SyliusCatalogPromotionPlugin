<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Sylius\Component\Core\Model\ChannelPricingInterface as BaseChannelPricingInterface;

interface ChannelPricingInterface extends BaseChannelPricingInterface
{
    public function getMultiplier(): float;
}
