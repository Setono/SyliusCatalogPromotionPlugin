<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\EntityManager;
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

    /** @var ProducerInterface */
    protected $producer;

    /** @var EntityRepository */
    protected $repository;

    /** @var ChannelPricingRecalculateHandler */
    protected $recalculateHandler;

    /**
     * Required for cleanup
     *
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(
        ProducerInterface $producer,
        EntityRepository $repository,
        ChannelPricingRecalculateHandler $recalculateHandler,
        EntityManager $entityManager
    ) {
        parent::__construct();

        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;
        $this->entityManager = $entityManager;
    }

    public function handleChannelPricing(ChannelPricingInterface $subject): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $subject->getId()
        );
    }

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

        $this->entityManager->flush();
        $this->entityManager->clear();

        return self::ACK;
    }

    public static function getSubscribedTopics()
    {
        return [
            self::EVENT,
        ];
    }
}
