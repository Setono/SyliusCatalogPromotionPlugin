<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

/**
 * Class EligibleSpecialsReassignAsyncHandler
 */
class EligibleSpecialsReassignAsyncHandler extends AbstractProductHandler implements EligibleSpecialsReassignHandlerInterface, PsrProcessor, TopicSubscriberInterface
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
     *
     * @param ProducerInterface $producer
     * @param ProductRepository $repository
     * @param EligibleSpecialsReassignHandler $handler
     */
    public function __construct(
        ProducerInterface $producer,
        ProductRepository $repository,
        EligibleSpecialsReassignHandler $handler
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function handleProduct(ProductInterface $product): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $product->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var ProductInterface $product */
        $product = $this->repository->find(
            $message->getBody()
        );

        if (!$product instanceof ProductInterface) {
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
