<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Component\Core\Model\Product;

/**
 * Class ProductRecalculateAsyncHandler
 */
class ProductRecalculateAsyncHandler extends AbstractHandler
    implements ProductRecalculateHandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    const EVENT = 'setono_sylius_bulk_specials_topic_product_recalculate';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * @var ProductRecalculateHandler
     */
    protected $recalculateHandler;

    /**
     * ProductRecalculateAsyncHandler constructor.
     * @param ProducerInterface $producer
     * @param ProductRepository $repository
     * @param ProductRecalculateHandler $recalculateHandler
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ProducerInterface $producer,
        ProductRepository $repository,
        ProductRecalculateHandler $recalculateHandler,
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
    public function handle(SpecialSubjectInterface $subject): void
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
        /** @var SpecialSubjectInterface|Product $product */
        $product = $this->repository->find(
            $message->getBody()
        );

        if (!$product instanceof SpecialSubjectInterface) {
            return self::REJECT;
        }

        $this->recalculateHandler->handle($product);

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
