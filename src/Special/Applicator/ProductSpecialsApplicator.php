<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Applicator;

use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * Class ProductSpecialsApplicator
 */
class ProductSpecialsApplicator
{
    /**
     * @var EntityRepository
     */
    protected $channelPricingRepository;

    /**
     * ProductSpecialsApplicator constructor.
     *
     * @param EntityRepository $channelPricingRepository
     */
    public function __construct(
        EntityRepository $channelPricingRepository
    ) {
        $this->channelPricingRepository = $channelPricingRepository;
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

        $this->channelPricingRepository->add($channelPricing);
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
        foreach ($product->getActiveSpecialsForChannelCode($channelCode)->toArray() as $special) {
            $multiplier *= $special->getMultiplier();
        }

        return $multiplier;
    }
}
