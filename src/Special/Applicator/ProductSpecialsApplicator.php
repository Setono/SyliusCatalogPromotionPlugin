<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Special\Applicator;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;

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
     * @param Product|SpecialSubjectInterface $product
     */
    public function applyToProduct(Product $product)
    {
        /** @var ProductVariant $variant */
        foreach ($product->getVariants() as $variant) {
            /** @var ChannelPricingInterface $channelPricing */
            foreach ($variant->getChannelPricings() as $channelPricing) {
                $this->applyToChannelPricing($channelPricing);
                $this->channelPricingRepository->add($channelPricing);
            }
        }
    }

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function applyToChannelPricing(ChannelPricingInterface $channelPricing)
    {
        /** @var Product|SpecialSubjectInterface $product */
        $product = $channelPricing->getProductVariant()->getProduct();
        $this->applyMultiplierToChannelPricing(
            $channelPricing,
            $this->getProductMultiplierForChannelCode(
                $product,
                $channelPricing->getChannelCode()
            )
        );
    }

    /**
     * @param ChannelPricingInterface $channelPricing
     * @param float $multiplier
     */
    protected function applyMultiplierToChannelPricing(ChannelPricingInterface $channelPricing, float $multiplier): void
    {
        $channelPricing->setPrice(
            (int) ($channelPricing->getOriginalPrice() * $multiplier)
        );
    }

    /**
     * @param Product|SpecialSubjectInterface $product
     * @param string $channelCode
     *
     * @return float
     */
    protected function getProductMultiplierForChannelCode(Product $product, string $channelCode): float
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
