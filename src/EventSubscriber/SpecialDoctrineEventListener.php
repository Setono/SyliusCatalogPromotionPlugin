<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Class SpecialDoctrineEventListener
 */
class SpecialDoctrineEventListener
{
    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * SpecialDoctrineEventSubscriber constructor.
     *
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
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof SpecialInterface) {
            return;
        }

        $this->specialRecalculateHandler->handle($entity);
    }
}
