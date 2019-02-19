<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Applicator;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductSpecialsApplicator
{
    /**
     * @var EntityManager
     */
    protected $channelPricingManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param EntityManager $channelPricingManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $channelPricingManager,
        LoggerInterface $logger
    ) {
        $this->channelPricingManager = $channelPricingManager;
        $this->logger = $logger;
    }

    /**
     * @param string $message
     */
    protected function log(string $message): void
    {
        $this->logger->info($message);
    }

    /**
     * @param ProductInterface $product
     */
    public function applyToProduct(ProductInterface $product): void
    {
        /** @var ProductVariant $variant */
        foreach ($product->getVariants() as $variant) {
            /** @var ChannelPricingInterface $channelPricing */
            foreach ($variant->getChannelPricings() as $channelPricing) {
                $this->applyToChannelPricing($channelPricing);
            }
        }
    }

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function applyToChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        $productVariant = $channelPricing->getProductVariant();
        if (!$productVariant instanceof ProductVariantInterface) {
            return;
        }

        $product = $productVariant->getProduct();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $this->applyMultiplierToChannelPricing(
            $channelPricing,
            $this->getProductMultiplierForChannelCode(
                $product,
                $channelPricing->getChannelCode()
            )
        );

        $this->channelPricingManager->persist($channelPricing);
    }

    /**
     * @param ChannelPricingInterface $channelPricing
     * @param float $multiplier
     */
    protected function applyMultiplierToChannelPricing(ChannelPricingInterface $channelPricing, float $multiplier): void
    {
        if (!$channelPricing->getOriginalPrice()) {
            if (!$channelPricing->getPrice()) {
                return;
            }

            $channelPricing->setOriginalPrice(
                $channelPricing->getPrice()
            );
        }

        $channelPricing->setPrice(
            (int) ($channelPricing->getOriginalPrice() * $multiplier)
        );

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $channelPricing->getProductVariant();
        $this->log(sprintf(
            "ChannelPricing for ProductVariant '%s': %s x %s = %s.",
            $productVariant->getCode(),
            $channelPricing->getOriginalPrice(),
            $multiplier,
            $channelPricing->getPrice()
        ));
    }

    /**
     * @param ProductInterface $product
     * @param string $channelCode
     *
     * @return float
     */
    protected function getProductMultiplierForChannelCode(ProductInterface $product, string $channelCode): float
    {
        if ($product->hasExclusiveSpecialsForChannelCode($channelCode)) {
            return $product->getFirstExclusiveSpecialForChannelCode($channelCode)->getMultiplier();
        }

        $multiplier = 1;
        /** @var SpecialInterface $special */
        foreach ($product->getActiveSpecialsForChannelCode($channelCode) as $special) {
            $multiplier *= $special->getMultiplier();
        }

        return $multiplier;
    }
}
