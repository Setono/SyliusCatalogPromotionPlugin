<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;
use Sylius\Component\Core\Model\ChannelPricingInterface;

/**
 * Class ChannelPricingRecalculateHandler
 */
class ChannelPricingRecalculateHandler extends AbstractChannelPricingHandler
{
    /**
     * @var ProductSpecialsApplicator
     */
    protected $productSpecialsApplicator;

    /**
     * ChannelPricingRecalculateHandler constructor.
     * @param ProductSpecialsApplicator $productSpecialsApplicator
     */
    public function __construct(
        ProductSpecialsApplicator $productSpecialsApplicator
    ) {
        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handleChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $this->productSpecialsApplicator->applyToChannelPricing($channelPricing);
    }
}
