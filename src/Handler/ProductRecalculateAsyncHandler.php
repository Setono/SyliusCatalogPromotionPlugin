<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

class ProductRecalculateAsyncHandler extends AbstractProductHandler implements PsrProcessor, TopicSubscriberInterface
{
    public const EVENT = 'setono_sylius_bulk_specials_topic_product_recalculate';

    /** @var ProducerInterface */
    protected $producer;

    /** @var ProductRepository */
    protected $repository;

    /** @var ProductRecalculateHandler */
    protected $recalculateHandler;

    /**
     * Required for cleanup
     *
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(
        ProducerInterface $producer,
        ProductRepository $repository,
        ProductRecalculateHandler $recalculateHandler,
        EntityManager $entityManager
    ) {
        parent::__construct();

        $this->producer = $producer;
        $this->repository = $repository;
        $this->recalculateHandler = $recalculateHandler;
        $this->entityManager = $entityManager;
    }

    public function handleProduct(ProductInterface $product): void
    {
        $this->producer->sendEvent(
            self::EVENT,
            $product->getId()
        );
    }

    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var ProductInterface $product */
        $product = $this->repository->find(
            $message->getBody()
        );

        if (!$product instanceof ProductInterface) {
            return self::REJECT;
        }

        $this->recalculateHandler->handle($product);

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
