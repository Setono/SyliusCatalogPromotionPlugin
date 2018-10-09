<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

/**
 * Class EligibleSpecialsReassignAsyncHandler
 */
class EligibleSpecialsReassignAsyncHandler extends AbstractHandler
    implements EligibleSpecialsReassignHandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    const EVENT = 'setono_sylius_bulk_specials_topic_reassign_specials';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * @var EligibleSpecialsReassignHandler
     */
    protected $handler;

    /**
     * EligibleSpecialsReassignAsyncHandler constructor.
     * @param ProducerInterface $producer
     * @param ProductRepository $repository
     * @param EligibleSpecialsReassignHandler $handler
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ProducerInterface $producer,
        ProductRepository $repository,
        EligibleSpecialsReassignHandler $handler,
        LoggerInterface $logger = null
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->handler = $handler;

        parent::__construct($logger);

        $this->log('Initialized');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SpecialSubjectInterface $subject): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $subject->getId()
        );

        $this->log(sprintf(
            'Event %s was sent with ID %s',
            self::EVENT,
            $subject->getId()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        $this->log(sprintf(
            'Event %s was received with Body %s',
            self::EVENT,
            $message->getBody()
        ));

        /** @var ProductInterface $product */
        $product = $this->repository->find(
            $message->getBody()
        );

        if (!$product instanceof SpecialSubjectInterface) {
            return self::REJECT;
        }

        $this->handler->handle($product);

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
