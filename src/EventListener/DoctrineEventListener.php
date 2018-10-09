<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\EligibleSpecialsReassignHandler;
use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\ChannelPricingRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ProductRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\Product;

/**
 * Class ProductEventListener
 *
 * @todo Decide to implement preUpdateTaxon in case Taxon's code could be changed
 *       - remove all specials that contain old taxon code in rules config AND assign new specials that match new rule
 *       - OR update rule with new taxon code?
 *       - OR ignore as far as Taxon's code can't be updated via admin
 *         AND user always can manually run specific bulk actions
 * @todo Decide to implement preUpdateProduct in case Product's code could be changed
 *       - reassign specials
 *       - OR ignore as far as Product's code can't be updated via admin
 *         AND user always can manually run specific bulk actions
 */
class DoctrineEventListener implements EventSubscriber
{
    /**
     * @var ChannelPricingRecalculateHandlerInterface
     */
    protected $channelPricingRecalculateHandler;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * @var EligibleSpecialsReassignHandler
     */
    protected $eligibleSpecialsReassignHandler;

    /**
     * DoctrineEventListener constructor.
     * @param ChannelPricingRecalculateHandlerInterface $channelPricingRecalculateHandler
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     * @param SpecialRecalculateHandlerInterface $specialRecalculateHandler
     * @param EligibleSpecialsReassignHandler $eligibleSpecialsReassignHandler
     */
    public function __construct(
        ChannelPricingRecalculateHandlerInterface $channelPricingRecalculateHandler,
        ProductRecalculateHandlerInterface $productRecalculateHandler,
        SpecialRecalculateHandlerInterface $specialRecalculateHandler,
        EligibleSpecialsReassignHandler $eligibleSpecialsReassignHandler
    ) {
        $this->channelPricingRecalculateHandler = $channelPricingRecalculateHandler;
        $this->productRecalculateHandler = $productRecalculateHandler;
        $this->specialRecalculateHandler = $specialRecalculateHandler;
        $this->eligibleSpecialsReassignHandler = $eligibleSpecialsReassignHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate' => [
                'preUpdateChannelPricing',
                'preUpdateSpecial',
            ],
            'postPersist' => [
                'postPersistProduct',
                'postPersistSpecial',
            ],
        ];
    }

    /**
     * On Product creation - assign eligible specials
     * (it starts recalculate automatically)
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersistProduct(LifecycleEventArgs $args)
    {
        /** @var Product|SpecialSubjectInterface $entity */
        $entity = $args->getObject();
        if (!$entity instanceof SpecialSubjectInterface) {
            return;
        }

        $this->eligibleSpecialsReassignHandler->handle($entity);
    }

    /**
     * On ChannelPricing's originalPrice update
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdateChannelPricing(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof ChannelPricing) {
            return;
        }

        if ($args->getOldValue('originalPrice') != $args->getNewValue('originalPrice')) {

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

    /**
     * Recalculate special (all Products related to given Special)
     * if actionType or actionPercent changed
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdateSpecial(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Special) {
            return;
        }

        if ($args->getOldValue('actionType') != $args->getNewValue('actionType') ||
            $args->getOldValue('actionPercent') != $args->getNewValue('actionPercent')) {

            if ($this->specialRecalculateHandler instanceof SpecialRecalculateHandler) {
                // Important: This will not work with non-async handlers as far as
                // new values not yet applied and recalculation result will be the same as before

                // @todo Dirty workaround
                // - Store Special that should be recalculated
                // - At postUpdateSpecial handler - recalculate if given Special === storedSpecial
                return;
            }

            $this->specialRecalculateHandler->handle($entity);
        }
    }

    /**
     * On Special creation - recalculate
     * (this new special will be assigned to eligible products automatically)
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersistSpecial(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Special) {
            return;
        }

        $this->specialRecalculateHandler->handle($entity);
    }
}
