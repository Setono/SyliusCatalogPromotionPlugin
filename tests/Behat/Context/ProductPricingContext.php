<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Setono\SyliusBulkDiscountPlugin\Model\ProductInterface;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductPricingContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * CommandsContext constructor.
     * @param SharedStorage $sharedStorage
     */
    public function __construct(
        SharedStorage $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    private function getPriceFromString(string $price): int
    {
        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }

    /**
     * @param ProductInterface $product
     * @param null|ChannelInterface $channel
     * @return ChannelPricingInterface
     */
    private function getProductsFirstChannelPricing(ProductInterface $product, ?ChannelInterface $channel = null): ChannelPricingInterface
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();

        if ($channel instanceof ChannelInterface) {
            foreach ($productVariant->getChannelPricings() as $channelPricing) {
                if ($channelPricing->getChannelCode() == $channel->getCode()) {
                    return $channelPricing;
                }
            }

            throw new \InvalidArgumentException(sprintf(
                "Product '%s' have no price for channel '%s'",
                (string) $product,
                (string) $channel
            ));
        }

        /** @var ChannelPricingInterface $channelPricing */
        return $productVariant->getChannelPricings()->first();
    }

    /**
     * @Then its price should become :price
     * @Then price of product :product should become :price
     * @Then price of product :product on channel :channel should become :price
     * @Then its price still should be :price
     * @Then price of product :product still should be :price
     * @Then price of product :product on channel :channel still should be :price
     */
    public function itsPriceShouldBe(string $price, ?ProductInterface $product = null, ?ChannelInterface $channel = null)
    {
        if (null == $product) {
            /** @var ProductInterface $product */
            $product = $this->sharedStorage->get('product');
        }

        Assert::eq(
            $this->getProductsFirstChannelPricing($product, $channel)->getPrice(),
            $this->getPriceFromString($price)
        );
    }

    /**
     * @Then its original price should become :originalPrice
     * @Then original price of product :product should become :price
     * @Then original price of product :product on channel :channel should become :price
     * @Then its original price still should be :originalPrice
     * @Then original price of product :product still should be :price
     * @Then original price of product :product on channel :channel still should be :price
     */
    public function itsOriginalPriceShouldBe(string $originalPrice, ?ProductInterface $product = null, ?ChannelInterface $channel = null)
    {
        if (null == $product) {
            /** @var ProductInterface $product */
            $product = $this->sharedStorage->get('product');
        }

        Assert::eq(
            $this->getProductsFirstChannelPricing($product)->getOriginalPrice(),
            $this->getPriceFromString($originalPrice)
        );
    }

}
