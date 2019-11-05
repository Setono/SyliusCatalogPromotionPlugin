<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusBulkDiscountPlugin\Factory\DiscountRuleFactoryInterface;
use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Setono\SyliusBulkDiscountPlugin\Repository\DiscountRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Test\Factory\TestDiscountFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class DiscountContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DiscountRuleFactoryInterface
     */
    private $discountRuleFactory;

    /**
     * @var TestDiscountFactoryInterface
     */
    private $testDiscountFactory;

    /**
     * @var DiscountRepositoryInterface
     */
    private $discountRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        DiscountRuleFactoryInterface $discountRuleFactory,
        TestDiscountFactoryInterface $testDiscountFactory,
        DiscountRepositoryInterface $discountRepository,
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
    public function thisDiscountDisabled(DiscountInterface $discount)
    {
        $discount->setEnabled(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) was enabled$/
     */
    public function thisDiscountEnabled(DiscountInterface $discount)
    {
        $discount->setEnabled(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) has already expired$/
     */
    public function thisDiscountHasExpired(DiscountInterface $discount)
    {
        $discount->setEndsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) expires tomorrow$/
     */
    public function thisDiscountExpiresTomorrow(DiscountInterface $discount)
    {
        $discount->setEndsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) has started yesterday$/
     */
    public function thisDiscountHasStartedYesterday(DiscountInterface $discount)
    {
        $discount->setStartsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this discount) starts tomorrow$/
     */
    public function thisDiscountStartsTomorrow(DiscountInterface $discount)
    {
        $discount->setStartsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount$/
     */
    public function itGivesPercentageDiscount(DiscountInterface $discount, $percentage)
    {
        $this->persistDiscount(
            $this->setPercentageDiscount($discount, $percentage)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") margin$/
     */
    public function itGivesPercentageMargin(DiscountInterface $discount, $margin)
    {
        $this->persistDiscount(
            $this->setPercentageMargin($discount, $margin)
        );
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("[^"]+%") off on every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        DiscountInterface $discount,
        $percentage,
        TaxonInterface $taxon
    ) {
        $this->createPercentageDiscount(
            $discount,
            $percentage,
            $this->discountRuleFactory->createHasTaxon([
                $taxon->getCode()
            ])
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAs(
        DiscountInterface $discount,
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
        DiscountInterface $discount,
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
        DiscountInterface $discount,
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
    public function discountApplicableForAllChannels(DiscountInterface $discount, array $channels)
    {
        foreach ($channels as $channel) {
            $discount->addChannel($channel);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the discount) was disabled for the (channel "[^"]+")$/
     */
    public function theDiscountWasDisabledForTheChannel(DiscountInterface $discount, ChannelInterface $channel)
    {
        $discount->removeChannel($channel);

        $this->objectManager->flush();
    }

    /**
     * @param DiscountInterface $discount
     * @param float $discount
     * @param DiscountRuleInterface $rule
     */
    private function createPercentageDiscount(
        DiscountInterface $discount,
        $percentage,
        DiscountRuleInterface $rule = null
    ) {
        $this->persistDiscount(
            $this->setPercentageDiscount($discount, $percentage),
            $rule
        );
    }

    /**
     * @param DiscountInterface $discount
     * @param int $actionPercent
     * @param string $actionType
     * @param DiscountRuleInterface|null $rule
     */
    private function persistDiscount(DiscountInterface $discount, DiscountRuleInterface $rule = null)
    {
        if (null !== $rule) {
            $discount->addRule($rule);
        }

        $this->objectManager->flush();
    }

    /**
     * @param DiscountInterface $discount
     * @param float $discount
     * @return DiscountInterface
     */
    private function setPercentageDiscount(DiscountInterface $discount, float $percentage): DiscountInterface
    {
        $discount->setActionType(Discount::ACTION_TYPE_OFF);
        $discount->setActionPercent($percentage * 100);

        return $discount;
    }

    /**
     * @param DiscountInterface $discount
     * @param float $margin
     * @return DiscountInterface
     */
    private function setPercentageMargin(DiscountInterface $discount, float $margin): DiscountInterface
    {
        $discount->setActionType(Discount::ACTION_TYPE_INCREASE);
        $discount->setActionPercent($margin * 100);

        return $discount;
    }
}
