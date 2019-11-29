<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusCatalogPromotionsPlugin\Factory\PromotionRuleFactoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Model\Promotion;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionsPlugin\Model\PromotionRuleInterface;
use Setono\SyliusCatalogPromotionsPlugin\Repository\PromotionRepositoryInterface;
use Setono\SyliusCatalogPromotionsPlugin\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class DiscountContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PromotionRuleFactoryInterface */
    private $discountRuleFactory;

    /** @var TestPromotionFactoryInterface */
    private $testDiscountFactory;

    /** @var PromotionRepositoryInterface */
    private $discountRepository;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionRuleFactoryInterface $discountRuleFactory,
        TestPromotionFactoryInterface $testDiscountFactory,
        PromotionRepositoryInterface $discountRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->discountRuleFactory = $discountRuleFactory;
        $this->testDiscountFactory = $testDiscountFactory;
        $this->discountRepository = $discountRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is (also) a discount :discountName
     * @Given there is (also) a discount :discountName applicable for :channel channel
     * @Given there is a discount :discountName identified by :discountCode code
     */
    public function thereIsDiscount(string $discountName, ?string $discountCode = null, ?ChannelInterface $channel = null): void
    {
        if (null === $channel) {
            $channel = $this->sharedStorage->get('channel');
        }

        $discount = $this->testDiscountFactory
            ->createForChannel($discountName, $channel)
        ;

        if (null !== $discountCode) {
            $discount->setCode($discountCode);
        }

        $this->discountRepository->add($discount);
        $this->sharedStorage->set('discount', $discount);
    }

    /**
     * @Given /^there is a discount "([^"]+)" with priority ([^"]+)$/
     */
    public function thereIsADiscountWithPriority($discountName, $priority)
    {
        $discount = $this->testDiscountFactory
            ->createForChannel($discountName, $this->sharedStorage->get('channel'))
        ;

        $discount->setPriority((int) $priority);
        $discount->setActionPercent(1); // todo should be moved to another method

        $this->discountRepository->add($discount);
        $this->sharedStorage->set('discount', $discount);
    }

    /**
     * @Given /^there is an exclusive discount "([^"]+)"(?:| with priority ([^"]+))$/
     */
    public function thereIsAnExclusiveDiscountWithPriority($discountName, $priority = 0)
    {
        $discount = $this->testDiscountFactory
            ->createForChannel($discountName, $this->sharedStorage->get('channel'))
        ;

        $discount->setExclusive(true);
        $discount->setPriority((int) $priority);

        $this->discountRepository->add($discount);
        $this->sharedStorage->set('discount', $discount);
    }

    /**
     * @Given /^(this discount) was disabled$/
     */
    public function thisDiscountDisabled(PromotionInterface $discount)
    {
        $discount->setEnabled(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) was enabled$/
     */
    public function thisDiscountEnabled(PromotionInterface $discount)
    {
        $discount->setEnabled(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) has already expired$/
     */
    public function thisDiscountHasExpired(PromotionInterface $discount)
    {
        $discount->setEndsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) expires tomorrow$/
     */
    public function thisDiscountExpiresTomorrow(PromotionInterface $discount)
    {
        $discount->setEndsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) has started yesterday$/
     */
    public function thisDiscountHasStartedYesterday(PromotionInterface $discount)
    {
        $discount->setStartsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) starts tomorrow$/
     */
    public function thisDiscountStartsTomorrow(PromotionInterface $discount)
    {
        $discount->setStartsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount$/
     */
    public function itGivesPercentageDiscount(PromotionInterface $discount, $percentage)
    {
        $this->persistDiscount(
            $this->setPercentageDiscount($discount, $percentage)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") margin$/
     */
    public function itGivesPercentageMargin(PromotionInterface $discount, $margin)
    {
        $this->persistDiscount(
            $this->setPercentageMargin($discount, $margin)
        );
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("[^"]+%") off on every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        PromotionInterface $discount,
        $percentage,
        TaxonInterface $taxon
    ) {
        $this->createPercentageDiscount(
            $discount,
            $percentage,
            $this->discountRuleFactory->createHasTaxon([
                $taxon->getCode(),
            ])
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAs(
        PromotionInterface $discount,
        $percentage,
        array $discountTaxons
    ) {
        $discountTaxonsCodes = [$discountTaxons[0]->getCode(), $discountTaxons[1]->getCode()];
        $this->createPercentageDiscount(
            $discount,
            $percentage,
            $this->discountRuleFactory->createHasTaxon($discountTaxonsCodes)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]*" product)$/
     * @Given /^([^"]+) gives ("[^"]+%") off on that product$/
     */
    public function itGivesPercentageDiscountOffOnAProduct(
        PromotionInterface $discount,
        $percentage,
        ?ProductInterface $product = null
    ) {
        if (null == $product) {
            $product = $this->sharedStorage->get('product');
        }

        $this->createPercentageDiscount(
            $discount,
            $percentage,
            $this->discountRuleFactory->createContainsProduct($product->getCode())
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]+" or "[^"]+" product)$/
     */
    public function itGivesPercentageDiscountOffOnAProducts(
        PromotionInterface $discount,
        $percentage,
        array $products
    ) {
        $productCodes = [$products[0]->getCode(), $products[1]->getCode()];
        $this->createPercentageDiscount(
            $discount,
            $percentage,
            $this->discountRuleFactory->createContainsProducts($productCodes)
        );
    }

    /**
     * @Given /^(this discount) applicable for (all channels)$/
     * @Given /^discount :discount applicable for (all channels)$/
     */
    public function discountApplicableForAllChannels(PromotionInterface $discount, array $channels)
    {
        foreach ($channels as $channel) {
            $discount->addChannel($channel);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the discount) was disabled for the (channel "[^"]+")$/
     */
    public function theDiscountWasDisabledForTheChannel(PromotionInterface $discount, ChannelInterface $channel)
    {
        $discount->removeChannel($channel);

        $this->objectManager->flush();
    }

    /**
     * @param float $discount
     * @param PromotionRuleInterface $rule
     */
    private function createPercentageDiscount(
        PromotionInterface $discount,
        $percentage,
        PromotionRuleInterface $rule = null
    ) {
        $this->persistDiscount(
            $this->setPercentageDiscount($discount, $percentage),
            $rule
        );
    }

    private function persistDiscount(PromotionInterface $discount, PromotionRuleInterface $rule = null)
    {
        if (null !== $rule) {
            $discount->addRule($rule);
        }

        $this->objectManager->flush();
    }

    /**
     * @param float $discount
     */
    private function setPercentageDiscount(PromotionInterface $discount, float $percentage): PromotionInterface
    {
        $discount->setActionType(Promotion::ACTION_TYPE_OFF);
        $discount->setActionPercent($percentage * 100);

        return $discount;
    }

    private function setPercentageMargin(PromotionInterface $discount, float $margin): PromotionInterface
    {
        $discount->setActionType(Promotion::ACTION_TYPE_INCREASE);
        $discount->setActionPercent($margin * 100);

        return $discount;
    }
}
