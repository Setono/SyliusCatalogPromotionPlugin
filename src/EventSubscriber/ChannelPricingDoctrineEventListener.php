<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandlerInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;

class ChannelPricingDoctrineEventListener
{
    /**
     * @var ChannelPricingRecalculateHandlerInterface
     */
    protected $channelPricingRecalculateHandler;

    /**
     * @var array|ChannelPricingInterface[]
     */
    private $channelPricingsToRecalculate = [];

    /**
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
        if (!$entity instanceof ChannelPricingInterface) {
            return;
        }

        if ($args->hasChangedField('originalPrice') && $args->getOldValue('originalPrice') !== $args->getNewValue('originalPrice')) {
            if ($this->channelPricingRecalculateHandler instanceof ChannelPricingRecalculateHandler) {
                // Store to recalculate after flush
                $this->channelPricingsToRecalculate[$entity->getId()] = $entity;

                return;
            }

            $this->channelPricingRecalculateHandler->handle($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof ChannelPricingInterface) {
            return;
        }

        if (isset($this->channelPricingsToRecalculate[$entity->getId()])) {
            unset($this->channelPricingsToRecalculate[$entity->getId()]);

            $this->channelPricingRecalculateHandler->handle($entity);
        }
    }
}
