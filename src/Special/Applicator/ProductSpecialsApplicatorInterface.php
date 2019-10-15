<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Applicator;

use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;

interface ProductSpecialsApplicatorInterface
{
    public function applyToProduct(ProductInterface $product): void;

    public function applyToChannelPricing(ChannelPricingInterface $channelPricing): void;
}
