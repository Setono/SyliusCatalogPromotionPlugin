<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusBulkDiscountPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusBulkDiscountPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkDiscountPlugin\Model\Discount;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountInterface;
use Setono\SyliusBulkDiscountPlugin\Model\DiscountRuleInterface;
use Setono\SyliusBulkDiscountPlugin\Special\Factory\SpecialRuleFactoryInterface;
use Setono\SyliusBulkDiscountPlugin\Test\Factory\TestSpecialFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class SpecialContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SpecialRuleFactoryInterface
     */
    private $ruleFactory;

    /**
     * @var TestSpecialFactoryInterface
     */
    private $testSpecialFactory;

    /**
     * @var SpecialRepositoryInterface
     */
    private $specialRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * SpecialContext constructor.
     * @param SharedStorageInterface $sharedStorage
     * @param SpecialRuleFactoryInterface $ruleFactory
     * @param TestSpecialFactoryInterface $testSpecialFactory
     * @param SpecialRepositoryInterface $specialRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SpecialRuleFactoryInterface $ruleFactory,
        TestSpecialFactoryInterface $testSpecialFactory,
        SpecialRepositoryInterface $specialRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->ruleFactory = $ruleFactory;
        $this->testSpecialFactory = $testSpecialFactory;
        $this->specialRepository = $specialRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is (also) a special :specialName
     * @Given there is (also) a special :specialName applicable for :channel channel
     * @Given there is a special :specialName identified by :specialCode code
     */
    public function thereIsSpecial(string $specialName, ?string $specialCode = null, ?ChannelInterface $channel = null): void
    {
        if (null === $channel) {
            $channel = $this->sharedStorage->get('channel');
        }

        $special = $this->testSpecialFactory
            ->createForChannel($specialName, $channel)
        ;

        if (null !== $specialCode) {
            $special->setCode($specialCode);
        }

        $this->specialRepository->add($special);
        $this->sharedStorage->set('special', $special);
    }

    /**
     * @Given /^there is a special "([^"]+)" with priority ([^"]+)$/
     */
    public function thereIsASpecialWithPriority($specialName, $priority)
    {
        $special = $this->testSpecialFactory
            ->createForChannel($specialName, $this->sharedStorage->get('channel'))
        ;

        $special->setPriority((int) $priority);

        $this->specialRepository->add($special);
        $this->sharedStorage->set('special', $special);
    }

    /**
     * @Given /^there is an exclusive special "([^"]+)"(?:| with priority ([^"]+))$/
     */
    public function thereIsAnExclusiveSpecialWithPriority($specialName, $priority = 0)
    {
        $special = $this->testSpecialFactory
            ->createForChannel($specialName, $this->sharedStorage->get('channel'))
        ;

        $special->setExclusive(true);
        $special->setPriority((int) $priority);

        $this->specialRepository->add($special);
        $this->sharedStorage->set('special', $special);
    }

    /**
     * @Given /^(this special) was disabled$/
     */
    public function thisSpecialDisabled(DiscountInterface $special)
    {
        $special->setEnabled(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this special) was enabled$/
     */
    public function thisSpecialEnabled(DiscountInterface $special)
    {
        $special->setEnabled(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this special) has already expired$/
     */
    public function thisSpecialHasExpired(DiscountInterface $special)
    {
        $special->setEndsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this special) expires tomorrow$/
     */
    public function thisSpecialExpiresTomorrow(DiscountInterface $special)
    {
        $special->setEndsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this special) has started yesterday$/
     */
    public function thisSpecialHasStartedYesterday(DiscountInterface $special)
    {
        $special->setStartsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this special) starts tomorrow$/
     */
    public function thisSpecialStartsTomorrow(DiscountInterface $special)
    {
        $special->setStartsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount$/
     */
    public function itGivesPercentageDiscount(DiscountInterface $special, $discount)
    {
        $this->persistSpecial(
            $this->setPercentageDiscount($special, $discount)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") margin$/
     */
    public function itGivesPercentageMargin(DiscountInterface $special, $margin)
    {
        $this->persistSpecial(
            $this->setPercentageMargin($special, $margin)
        );
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("[^"]+%") off on every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        DiscountInterface $special,
        $discount,
        TaxonInterface $taxon
    ) {
        $this->createPercentageSpecial(
            $special,
            $discount,
            $this->ruleFactory->createHasTaxon([
                $taxon->getCode()
            ])
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAs(
        DiscountInterface $special,
        $discount,
        array $discountTaxons
    ) {
        $discountTaxonsCodes = [$discountTaxons[0]->getCode(), $discountTaxons[1]->getCode()];
        $this->createPercentageSpecial(
            $special,
            $discount,
            $this->ruleFactory->createHasTaxon($discountTaxonsCodes)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]*" product)$/
     * @Given /^([^"]+) gives ("[^"]+%") off on that product$/
     */
    public function itGivesPercentageDiscountOffOnAProduct(
        DiscountInterface $special,
        $discount,
        ?ProductInterface $product = null
    ) {
        if (null == $product) {
            $product = $this->sharedStorage->get('product');
        }

        $this->createPercentageSpecial(
            $special,
            $discount,
            $this->ruleFactory->createContainsProduct($product->getCode())
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]+" or "[^"]+" product)$/
     */
    public function itGivesPercentageDiscountOffOnAProducts(
        DiscountInterface $special,
        $discount,
        array $products
    ) {
        $productCodes = [$products[0]->getCode(), $products[1]->getCode()];
        $this->createPercentageSpecial(
            $special,
            $discount,
            $this->ruleFactory->createContainsProducts($productCodes)
        );
    }

    /**
     * @Given /^(this special) applicable for (all channels)$/
     * @Given /^special :special applicable for (all channels)$/
     */
    public function specialApplicableForAllChannels(DiscountInterface $special, array $channels)
    {
        foreach ($channels as $channel) {
            $special->addChannel($channel);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the special) was disabled for the (channel "[^"]+")$/
     */
    public function theSpecialWasDisabledForTheChannel(DiscountInterface $special, ChannelInterface $channel)
    {
        $special->removeChannel($channel);

        $this->objectManager->flush();
    }

    /**
     * @param DiscountInterface $special
     * @param float $discount
     * @param DiscountRuleInterface $rule
     */
    private function createPercentageSpecial(
        DiscountInterface $special,
        $discount,
        DiscountRuleInterface $rule = null
    ) {
        $this->persistSpecial(
            $this->setPercentageDiscount($special, $discount),
            $rule
        );
    }

    /**
     * @param DiscountInterface $special
     * @param int $actionPercent
     * @param string $actionType
     * @param DiscountRuleInterface|null $rule
     */
    private function persistSpecial(DiscountInterface $special, DiscountRuleInterface $rule = null)
    {
        if (null !== $rule) {
            $special->addRule($rule);
        }

        $this->objectManager->flush();
    }

    /**
     * @param DiscountInterface $special
     * @param float $discount
     * @return DiscountInterface
     */
    private function setPercentageDiscount(DiscountInterface $special, float $discount): DiscountInterface
    {
        $special->setActionType(Discount::ACTION_TYPE_OFF);
        $special->setActionPercent($discount * 100);

        return $special;
    }

    /**
     * @param DiscountInterface $special
     * @param float $margin
     * @return DiscountInterface
     */
    private function setPercentageMargin(DiscountInterface $special, float $margin): DiscountInterface
    {
        $special->setActionType(Discount::ACTION_TYPE_INCREASE);
        $special->setActionPercent($margin * 100);

        return $special;
    }
}
