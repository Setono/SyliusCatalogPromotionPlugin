<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Message\Command\AssignEligibleSpecials;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductDoctrineEventListener
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * On Product creation - assign eligible specials
     * (it starts recalculate automatically)
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof ProductInterface) {
            return;
        }

        $this->commandBus->dispatch(new AssignEligibleSpecials($entity));
    }
}
