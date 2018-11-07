<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

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
     * @param ProducerInterface $producer
     * @param SpecialRepositoryInterface $repository
     * @param SpecialRecalculateHandler $recalculateHandler
     */
    public function __construct(
        ProducerInterface $producer,
        SpecialRepositoryInterface $repository,
        SpecialRecalculateHandler $recalculateHandler
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;
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
