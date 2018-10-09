<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\SpecialRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Class SpecialRecalculateAsyncHandler
 */
class SpecialRecalculateAsyncHandler extends AbstractHandler
    implements SpecialRecalculateHandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    const EVENT = 'setono_sylius_bulk_specials_topic_special_recalculate';

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
     * SpecialRecalculateAsyncHandler constructor.
     * @param ProducerInterface $producer
     * @param SpecialRepositoryInterface $repository
     * @param SpecialRecalculateHandler $recalculateHandler
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ProducerInterface $producer,
        SpecialRepositoryInterface $repository,
        SpecialRecalculateHandler $recalculateHandler,
        LoggerInterface $logger = null
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;

        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SpecialInterface $special): void
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
