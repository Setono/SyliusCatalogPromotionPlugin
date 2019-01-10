<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\EligibleSpecialsReassignHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;

class ProductDoctrineEventListener
{
    /**
     * @var EligibleSpecialsReassignHandlerInterface
     */
    protected $eligibleSpecialsReassignHandler;

    /**
     * @param EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
     */
    public function __construct(
        EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
    ) {
        $this->eligibleSpecialsReassignHandler = $eligibleSpecialsReassignHandler;
    }

    /**
     * On Product creation - assign eligible specials
     * (it starts recalculate automatically)
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof ProductInterface) {
            return;
        }

        $this->eligibleSpecialsReassignHandler->handleProduct($entity);
    }
}
