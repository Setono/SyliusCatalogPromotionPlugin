<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\ProductRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandler;
use Setono\SyliusBulkSpecialsPlugin\Handler\SpecialRecalculateHandlerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

class SpecialDoctrineEventListener
{
    /**
     * @var SpecialRecalculateHandlerInterface
     */
    protected $specialRecalculateHandler;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var EntityManager
     */
    protected $productManager;

    /**
     * @var array|SpecialInterface[]
     */
    private $specialsToRecalculate = [];

    /**
     * @param SpecialRecalculateHandlerInterface $specialRecalculateHandler
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     * @param ProductRepositoryInterface $productRepository
     * @param EntityManager $productManager
     */
    public function __construct(
        SpecialRecalculateHandlerInterface $specialRecalculateHandler,
        ProductRecalculateHandlerInterface $productRecalculateHandler,
        ProductRepositoryInterface $productRepository,
        EntityManager $productManager
    ) {
        $this->specialRecalculateHandler = $specialRecalculateHandler;
        $this->productRecalculateHandler = $productRecalculateHandler;
        $this->productRepository = $productRepository;
        $this->productManager = $productManager;
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

    /**
     * On Special remove - detach it from and recalculate all products related to it
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof SpecialInterface) {
            return;
        }

        $iterableResult = $this->productRepository->findBySpecialQB($entity)->getQuery()->iterate();
        foreach ($iterableResult as $productRow) {
            /** @var ProductInterface $product */
            $product = $productRow[0];

            if ($product->hasSpecial($entity)) {
                $product->removeSpecial($entity);
                $this->productManager->persist($product);
            }

            $this->productRecalculateHandler->handleProduct($product);
        }
    }
}
