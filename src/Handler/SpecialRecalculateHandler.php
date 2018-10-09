<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;

/**
 * Class SpecialRecalculateHandler
 */
class SpecialRecalculateHandler implements SpecialRecalculateHandlerInterface
{
    /**
     * Required for cleanup
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProductRepositoryInterface|ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * SpecialRecalculateHandler constructor.
     * @param EntityManager $entityManager
     * @param ProductRepositoryInterface $productRepository
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     */
    public function __construct(
        EntityManager $entityManager,
        ProductRepositoryInterface $productRepository,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->productRecalculateHandler = $productRecalculateHandler;
    }

    /**
     * @param SpecialInterface $special
     */
    public function handle(SpecialInterface $special): void
    {
        // @see Good explanation at https://stackoverflow.com/a/26698814
        $iterableResult = $this->productRepository->findBySpecialQB($special)->getQuery()->iterate();

        foreach ($iterableResult as $productRow) {
            /** @var SpecialSubjectInterface $product */
            $product = $productRow[0];

            if (!$product->hasSpecial($special)) {
                $product->addSpecial($special);
                $this->productRepository->add($product);
            }

            $this->productRecalculateHandler->handle($product);
            //$this->entityManager->clear();
        }
    }
}
