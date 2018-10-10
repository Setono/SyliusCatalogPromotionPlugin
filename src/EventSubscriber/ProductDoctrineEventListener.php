<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Setono\SyliusBulkSpecialsPlugin\Handler\EligibleSpecialsReassignHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Component\Core\Model\Product;

/**
 * Class ProductDoctrineEventListener
 */
class ProductDoctrineEventListener
{
    /**
     * @var EligibleSpecialsReassignHandlerInterface
     */
    protected $eligibleSpecialsReassignHandler;

    /**
     * ProductDoctrineEventSubscriber constructor.
     *
     * @param EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
     */
    public function __construct(
        EligibleSpecialsReassignHandlerInterface $eligibleSpecialsReassignHandler
    ) {
        $this->eligibleSpecialsReassignHandler = $eligibleSpecialsReassignHandler;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * On Product creation - assign eligible specials
     * (it starts recalculate automatically)
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var Product|SpecialSubjectInterface $entity */
        $entity = $args->getObject();
        if (!$entity instanceof SpecialSubjectInterface) {
            return;
        }

        $this->eligibleSpecialsReassignHandler->handle($entity);
    }
}
