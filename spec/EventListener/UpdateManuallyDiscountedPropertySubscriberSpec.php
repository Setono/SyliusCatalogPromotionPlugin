<?php

declare(strict_types=1);

namespace spec\Setono\SyliusCatalogPromotionPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusCatalogPromotionPlugin\EventListener\UpdateManuallyDiscountedPropertySubscriber;
use Setono\SyliusCatalogPromotionPlugin\Model\ChannelPricingInterface;

class UpdateManuallyDiscountedPropertySubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateManuallyDiscountedPropertySubscriber::class);
    }

    public function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    public function it_updates_property_when_price_has_changed(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $changeSet = ['price' => true];
        $event = new PreUpdateEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject(), $changeSet);

        $channelPricing->hasDiscount()->willReturn(true);
        $channelPricing->resetBulkIdentifier()->shouldBeCalled();
        $channelPricing->setManuallyDiscounted(true)->shouldBeCalled();

        $this->preUpdate($event);
    }

    public function it_updates_property_when_original_price_has_changed(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $changeSet = ['originalPrice' => true];
        $event = new PreUpdateEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject(), $changeSet);

        $channelPricing->hasDiscount()->willReturn(true);
        $channelPricing->resetBulkIdentifier()->shouldBeCalled();
        $channelPricing->setManuallyDiscounted(true)->shouldBeCalled();

        $this->preUpdate($event);
    }

    public function it_sets_property_to_false_when_prices_are_equal_although_properties_changed(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $changeSet = ['originalPrice' => true];
        $event = new PreUpdateEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject(), $changeSet);

        $channelPricing->hasDiscount()->willReturn(false);
        $channelPricing->resetBulkIdentifier()->shouldBeCalled();
        $channelPricing->setManuallyDiscounted(false)->shouldBeCalled();

        $this->preUpdate($event);
    }

    public function it_does_not_update_property_when_no_properties_has_changed(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $changeSet = [];
        $event = new PreUpdateEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject(), $changeSet);

        $channelPricing->setManuallyDiscounted(Argument::any())->shouldNotBeCalled();

        $this->preUpdate($event);
    }

    public function it_sets_property_to_false_when_prices_are_equal(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $event = new LifecycleEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject());

        $channelPricing->hasDiscount()->willReturn(false);
        $channelPricing->resetBulkIdentifier()->shouldBeCalled();
        $channelPricing->setManuallyDiscounted(false)->shouldBeCalled();

        $this->prePersist($event);
    }

    public function it_sets_property_to_true_when_prices_are_not_equal(
        ChannelPricingInterface $channelPricing,
        EntityManagerInterface $entityManager
    ): void {
        $event = new LifecycleEventArgs($channelPricing->getWrappedObject(), $entityManager->getWrappedObject());

        $channelPricing->hasDiscount()->willReturn(true);
        $channelPricing->resetBulkIdentifier()->shouldBeCalled();
        $channelPricing->setManuallyDiscounted(true)->shouldBeCalled();

        $this->prePersist($event);
    }
}
