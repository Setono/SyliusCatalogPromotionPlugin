<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

class SpecialDoctrineEventListener
{
    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * @var array|SpecialInterface[]
     */
    private $specialsToRecalculate = [];

    /**
     * @param SpecialRecalculateHandlerInterface $specialRecalculateHandler
     */
    public function __construct(
        SpecialRecalculateHandlerInterface $specialRecalculateHandler
    ) {
        $this->specialRecalculateHandler = $specialRecalculateHandler;
    }

    /**
     * Recalculate special (all Products related to given Special)
     * if actionType or actionPercent changed
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof SpecialInterface) {
            return;
        }

        if (($args->hasChangedField('actionType') && $args->getOldValue('actionType') !== $args->getNewValue('action_type')) ||
            ($args->hasChangedField('actionPercent') && $args->getOldValue('actionPercent') !== $args->getNewValue('actionPercent'))) {
            if ($this->specialRecalculateHandler instanceof SpecialRecalculateHandler) {
                $this->specialsToRecalculate[$entity->getId()] = $entity;

                return;
            }

            $this->specialRecalculateHandler->handle($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof SpecialInterface) {
            return;
        }

        if (isset($this->specialsToRecalculate[$entity->getId()])) {
            unset($this->specialsToRecalculate[$entity->getId()]);

            $this->specialRecalculateHandler->handle($entity);
        }
    }

    /**
     * On Special creation - recalculate
     * (this new special will be assigned to eligible products automatically)
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof SpecialInterface) {
            return;
        }

        $this->specialRecalculateHandler->handle($entity);
    }
}
