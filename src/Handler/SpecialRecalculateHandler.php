<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

/**
 * Class SpecialRecalculateHandler
 */
class SpecialRecalculateHandler extends AbstractSpecialHandler
{
    /**
     * Required for cleanup
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * SpecialRecalculateHandler constructor.
     *
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
     * {@inheritdoc}
     */
    public function handleSpecial(SpecialInterface $special): void
    {
        // @see Good explanation at https://stackoverflow.com/a/26698814
        $iterableResult = $this->productRepository->findBySpecialQB($special)->getQuery()->iterate();

        foreach ($iterableResult as $productRow) {
            /** @var ProductInterface $product */
            $product = $productRow[0];

            if (!$product->hasSpecial($special)) {
                $product->addSpecial($special);
                $this->productRepository->add($product);
            }

            $this->productRecalculateHandler->handleProduct($product);
        }
    }
}
