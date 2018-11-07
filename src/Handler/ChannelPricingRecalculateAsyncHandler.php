<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelPricingInterface;

class ChannelPricingRecalculateAsyncHandler extends AbstractChannelPricingHandler implements PsrProcessor, TopicSubscriberInterface
{
    public const EVENT = 'setono_sylius_bulk_specials_topic_channel_pricing_recalculate';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var ChannelPricingRecalculateHandler
     */
    protected $recalculateHandler;

    /**
     * ChannelPricingRecalculateAsyncHandler constructor.
     *
     * @param ProducerInterface $producer
     * @param EntityRepository $repository
     * @param ChannelPricingRecalculateHandler $recalculateHandler
     */
    public function __construct(
        ProducerInterface $producer,
        EntityRepository $repository,
        ChannelPricingRecalculateHandler $recalculateHandler
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handleChannelPricing(ChannelPricingInterface $subject): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $subject->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->repository->find(
            $message->getBody()
        );

        if (!$channelPricing instanceof ChannelPricingInterface) {
            return self::REJECT;
        }

        $this->recalculateHandler->handle($channelPricing);

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
