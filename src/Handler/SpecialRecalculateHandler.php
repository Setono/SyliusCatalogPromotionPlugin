<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM\ProductRepositoryInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

class SpecialRecalculateHandler extends AbstractSpecialHandler
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductRecalculateHandlerInterface
     */
    protected $productRecalculateHandler;

    /**
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param ProductRecalculateHandlerInterface $productRecalculateHandler
     */
    public function __construct(
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ProductRecalculateHandlerInterface $productRecalculateHandler
    ) {
        parent::__construct($logger);

        $this->productRepository = $productRepository;
        $this->productRecalculateHandler = $productRecalculateHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handleSpecial(SpecialInterface $special): void
    {
        $this->log(sprintf(
            "Special '%s' recalculate started...",
            (string) $special
        ));

        // @see Good explanation at https://stackoverflow.com/a/26698814
        $iterableResult = $this->productRepository->findBySpecialQB($special)->getQuery()->iterate();

        foreach ($iterableResult as $productRow) {
            /** @var ProductInterface $product */
            $product = $productRow[0];

            if (!$product->hasSpecial($special)) {
                $product->addSpecial($special);

                $this->log(sprintf(
                    "Special '%s' assigned to Product '%s'",
                    (string) $special,
                    (string) $product
                ));

                $this->productRepository->add($product);
            }

            $this->productRecalculateHandler->handleProduct($product);
        }

        $this->log(sprintf(
            "Special '%s' recalculate finished.",
            (string) $special
        ));
    }

}
