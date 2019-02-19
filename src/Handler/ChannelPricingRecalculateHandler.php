<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;
use Sylius\Component\Core\Model\ChannelPricingInterface;

class ChannelPricingRecalculateHandler extends AbstractChannelPricingHandler
{
    /**
     * @var ProductSpecialsApplicator
     */
    protected $productSpecialsApplicator;

    /**
     * @param LoggerInterface $logger
     * @param ProductSpecialsApplicator $productSpecialsApplicator
     */
    public function __construct(
        LoggerInterface $logger,
        ProductSpecialsApplicator $productSpecialsApplicator
    ) {
        parent::__construct($logger);

        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handleChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $this->log(sprintf(
            "ChannelPricing for Product '%s' recalculate started...",
            $channelPricing->getProductVariant()->getCode()
        ));

        $this->productSpecialsApplicator->applyToChannelPricing($channelPricing);

        $this->log(sprintf(
            "ChannelPricing for Product '%s' recalculate finished.",
            $channelPricing->getProductVariant()->getCode()
        ));
    }
}
