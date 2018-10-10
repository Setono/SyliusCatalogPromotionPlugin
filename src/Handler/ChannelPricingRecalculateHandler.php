<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
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
     * @var EntityRepository
     */
    protected $channelPricingRepository;

    /**
     * ChannelPricingRecalculateHandler constructor.
     *
     * @param ProductSpecialsApplicator $productSpecialsApplicator
     * @param EntityRepository $channelPricingRepository
     */
    public function __construct(
        ProductSpecialsApplicator $productSpecialsApplicator,
        EntityRepository $channelPricingRepository
    ) {
        $this->productSpecialsApplicator = $productSpecialsApplicator;
        $this->channelPricingRepository = $channelPricingRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handleChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $this->productSpecialsApplicator->applyToChannelPricing($channelPricing);
        $this->channelPricingRepository->add($channelPricing);
    }
}
