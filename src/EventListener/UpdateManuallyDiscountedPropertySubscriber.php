<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Setono\SyliusBulkSpecialsPlugin\Model\ChannelPricingInterface;

final class UpdateManuallyDiscountedPropertySubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        self::update($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        self::update($event);
    }

    private static function update(LifecycleEventArgs $event): void
    {
        $channelPricing = $event->getEntity();
        if (!$channelPricing instanceof ChannelPricingInterface) {
            return;
        }

        if ($event instanceof PreUpdateEventArgs
            && !$event->hasChangedField('price')
            && !$event->hasChangedField('originalPrice')
        ) {
            return;
        }

        $channelPricing->setManuallyDiscounted(
            null !== $channelPricing->getOriginalPrice()
            && $channelPricing->getPrice() !== $channelPricing->getOriginalPrice()
        );
    }
}
