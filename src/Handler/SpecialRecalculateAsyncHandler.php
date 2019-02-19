<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

class SpecialRecalculateAsyncHandler extends AbstractSpecialHandler implements PsrProcessor, TopicSubscriberInterface
{
    public const EVENT = 'setono_sylius_bulk_specials_topic_special_recalculate';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var SpecialRepositoryInterface
     */
    protected $repository;

    /**
     * @var SpecialRecalculateHandler
     */
    protected $recalculateHandler;

    /**
     * Required for cleanup
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ProducerInterface $producer
     * @param SpecialRepositoryInterface $repository
     * @param SpecialRecalculateHandler $recalculateHandler
     * @param EntityManager $entityManager
     */
    public function __construct(
        ProducerInterface $producer,
        SpecialRepositoryInterface $repository,
        SpecialRecalculateHandler $recalculateHandler,
        EntityManager $entityManager
    ) {
        parent::__construct();

        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handleSpecial(SpecialInterface $special): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $special->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var SpecialInterface $special */
        $special = $this->repository->find(
            $message->getBody()
        );

        if (!$special instanceof SpecialInterface) {
            return self::REJECT;
        }

        $this->recalculateHandler->handle($special);

        $this->entityManager->flush();
        $this->entityManager->clear();

        return self::ACK;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [
            self::EVENT,
        ];
    }
}
