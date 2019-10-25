<?php

declare(strict_types=1);

namespace AppBundle\Model;

use Setono\SyliusBulkSpecialsPlugin\Model\ChannelPricingInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ChannelPricingTrait;
use Sylius\Component\Core\Model\ChannelPricing as BaseChannelPricing;

class ChannelPricing extends BaseChannelPricing implements ChannelPricingInterface
{
    use ChannelPricingTrait;
}
