<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusCatalogPromotionPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusCatalogPromotionPlugin\Factory\PromotionRuleFactoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Model\Promotion;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionInterface;
use Setono\SyliusCatalogPromotionPlugin\Model\PromotionRuleInterface;
use Setono\SyliusCatalogPromotionPlugin\Repository\PromotionRepositoryInterface;
use Setono\SyliusCatalogPromotionPlugin\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class DiscountContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PromotionRuleFactoryInterface */
    private $promotionRuleFactory;

    /** @var TestPromotionFactoryInterface */
    private $testDiscountFactory;

    /** @var PromotionRepositoryInterface */
    private $promotionRepository;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionRuleFactoryInterface $promotionRuleFactory,
        TestPromotionFactoryInterface $testDiscountFactory,
        PromotionRepositoryInterface $promotionRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->promotionRuleFactory = $promotionRuleFactory;
        $this->testDiscountFactory = $testDiscountFactory;
        $this->promotionRepository = $promotionRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is (also) a promotion :promotionName
     * @Given there is (also) a promotion :promotionName applicable for :channel channel
     * @Given there is a promotion :promotionName identified by :promotionCode code
     */
    public function thereIsDiscount(string $promotionName, ?string $promotionCode = null, ?ChannelInterface $channel = null): void
    {
        if (null === $channel) {
            $channel = $this->sharedStorage->get('channel');
        }

        $promotion = $this->testDiscountFactory
            ->createForChannel($promotionName, $channel)
        ;

        if (null !== $promotionCode) {
            $promotion->setCode($promotionCode);
        }

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given /^there is a promotion "([^"]+)" with priority ([^"]+)$/
     */
    public function thereIsADiscountWithPriority($promotionName, $priority)
    {
        $promotion = $this->testDiscountFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        $promotion->setPriority((int) $priority);
        $promotion->setActionPercent(1); // todo should be moved to another method

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given /^there is an exclusive promotion "([^"]+)"(?:| with priority ([^"]+))$/
     */
    public function thereIsAnExclusiveDiscountWithPriority($promotionName, $priority = 0)
    {
        $promotion = $this->testDiscountFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        $promotion->setExclusive(true);
        $promotion->setPriority((int) $priority);

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given /^(this promotion) was disabled$/
     */
    public function thisDiscountDisabled(PromotionInterface $promotion)
    {
        $promotion->setEnabled(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) was enabled$/
     */
    public function thisDiscountEnabled(PromotionInterface $promotion)
    {
        $promotion->setEnabled(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) has already expired$/
     */
    public function thisDiscountHasExpired(PromotionInterface $promotion)
    {
        $promotion->setEndsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) expires tomorrow$/
     */
    public function thisDiscountExpiresTomorrow(PromotionInterface $promotion)
    {
        $promotion->setEndsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) has started yesterday$/
     */
    public function thisDiscountHasStartedYesterday(PromotionInterface $promotion)
    {
        $promotion->setStartsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) starts tomorrow$/
     */
    public function thisDiscountStartsTomorrow(PromotionInterface $promotion)
    {
        $promotion->setStartsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") promotion$/
     */
    public function itGivesPercentageDiscount(PromotionInterface $promotion, $percentage)
    {
        $this->persistDiscount(
            $this->setPercentageDiscount($promotion, $percentage)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") margin$/
     */
    public function itGivesPercentageMargin(PromotionInterface $promotion, $margin)
    {
        $this->persistDiscount(
            $this->setPercentageMargin($promotion, $margin)
        );
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("[^"]+%") off on every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        PromotionInterface $promotion,
        $percentage,
        TaxonInterface $taxon
    ) {
        $this->createPercentageDiscount(
            $promotion,
            $percentage,
            $this->promotionRuleFactory->createHasTaxon([
                $taxon->getCode(),
            ])
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAs(
        PromotionInterface $promotion,
        $percentage,
        array $promotionTaxons
    ) {
        $promotionTaxonsCodes = [$promotionTaxons[0]->getCode(), $promotionTaxons[1]->getCode()];
        $this->createPercentageDiscount(
            $promotion,
            $percentage,
            $this->promotionRuleFactory->createHasTaxon($promotionTaxonsCodes)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]*" product)$/
     * @Given /^([^"]+) gives ("[^"]+%") off on that product$/
     */
    public function itGivesPercentageDiscountOffOnAProduct(
        PromotionInterface $promotion,
        $percentage,
        ?ProductInterface $product = null
    ) {
        if (null == $product) {
            $product = $this->sharedStorage->get('product');
        }

        $this->createPercentageDiscount(
            $promotion,
            $percentage,
            $this->promotionRuleFactory->createContainsProduct($product->getCode())
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]+" or "[^"]+" product)$/
     */
    public function itGivesPercentageDiscountOffOnAProducts(
        PromotionInterface $promotion,
        $percentage,
        array $products
    ) {
        $productCodes = [$products[0]->getCode(), $products[1]->getCode()];
        $this->createPercentageDiscount(
            $promotion,
            $percentage,
            $this->promotionRuleFactory->createContainsProducts($productCodes)
        );
    }

    /**
     * @Given /^(this promotion) applicable for (all channels)$/
     * @Given /^promotion :promotion applicable for (all channels)$/
     */
    public function promotionApplicableForAllChannels(PromotionInterface $promotion, array $channels)
    {
        foreach ($channels as $channel) {
            $promotion->addChannel($channel);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the promotion) was disabled for the (channel "[^"]+")$/
     */
    public function theDiscountWasDisabledForTheChannel(PromotionInterface $promotion, ChannelInterface $channel)
    {
        $promotion->removeChannel($channel);

        $this->objectManager->flush();
    }

    /**
     * @param float $promotion
     * @param PromotionRuleInterface $rule
     */
    private function createPercentageDiscount(
        PromotionInterface $promotion,
        $percentage,
        PromotionRuleInterface $rule = null
    ) {
        $this->persistDiscount(
            $this->setPercentageDiscount($promotion, $percentage),
            $rule
        );
    }

    private function persistDiscount(PromotionInterface $promotion, PromotionRuleInterface $rule = null)
    {
        if (null !== $rule) {
            $promotion->addRule($rule);
        }

        $this->objectManager->flush();
    }

    /**
     * @param float $promotion
     */
    private function setPercentageDiscount(PromotionInterface $promotion, float $percentage): PromotionInterface
    {
        $promotion->setActionType(Promotion::ACTION_TYPE_OFF);
        $promotion->setActionPercent($percentage * 100);

        return $promotion;
    }

    private function setPercentageMargin(PromotionInterface $promotion, float $margin): PromotionInterface
    {
        $promotion->setActionType(Promotion::ACTION_TYPE_INCREASE);
        $promotion->setActionPercent($margin * 100);

        return $promotion;
    }
}
