<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandlerInterface;
use Sylius\Component\Core\Model\ChannelPricing;

/**
 * Class ChannelPricingDoctrineEventSubscriber
 */
class ChannelPricingDoctrineEventListener
{
    /**
     * @var ChannelPricingRecalculateHandlerInterface
     */
    protected $channelPricingRecalculateHandler;

    /**
     * ChannelPricingDoctrineEventSubscriber constructor.
     *
     * @param ChannelPricingRecalculateHandlerInterface $channelPricingRecalculateHandler
     */
    public function __construct(
        ChannelPricingRecalculateHandlerInterface $channelPricingRecalculateHandler
    ) {
        $this->channelPricingRecalculateHandler = $channelPricingRecalculateHandler;
    }

    /**
     * On ChannelPricing's originalPrice update
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof ChannelPricing) {
            return;
        }

        if ($args->hasChangedField('originalPrice') && $args->getOldValue('originalPrice') !== $args->getNewValue('originalPrice')) {
            if ($this->channelPricingRecalculateHandler instanceof ChannelPricingRecalculateHandler) {
                // Important: This will not work with non-async handlers as far as
                // new values not yet applied and recalculation result will be the same as before

                // @todo Dirty workaround
                // - Store ChannelPricing that should be recalculated
                // - At postUpdateSpecial handler - recalculate if given ChannelPricing === storedChannelPricing
                return;
            }

            $this->channelPricingRecalculateHandler->handle($entity);
        }
    }
}
