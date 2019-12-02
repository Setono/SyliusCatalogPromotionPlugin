<?php

declare(strict_types=1);

namespace AppBundle\Model;

use Setono\SyliusCatalogPromotionPlugin\Model\ChannelPricingInterface;
use Setono\SyliusCatalogPromotionPlugin\Model\ChannelPricingTrait;
use Sylius\Component\Core\Model\ChannelPricing as BaseChannelPricing;

class ChannelPricing extends BaseChannelPricing implements ChannelPricingInterface
{
    use ChannelPricingTrait;
}
