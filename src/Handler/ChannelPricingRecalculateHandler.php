<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ChannelPricingRecalculateHandler extends AbstractChannelPricingHandler
{
    /** @var ProductSpecialsApplicator */
    protected $productSpecialsApplicator;

    public function __construct(LoggerInterface $logger, ProductSpecialsApplicator $productSpecialsApplicator)
    {
        parent::__construct($logger);

        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * @throws StringsException
     * @throws ORMException
     */
    public function handleChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $channelPricing->getProductVariant();
        $this->log(sprintf(
            "ChannelPricing for ProductVariant '%s' recalculate started...",
            $productVariant->getCode()
        ));

        $this->productSpecialsApplicator->applyToChannelPricing($channelPricing);

        $this->log(sprintf(
            "ChannelPricing for Product '%s' recalculate finished.",
            $productVariant->getCode()
        ));
    }
}
