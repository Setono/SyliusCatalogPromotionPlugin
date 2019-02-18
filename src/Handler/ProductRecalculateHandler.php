<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Psr\Log\LoggerInterface;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;

class ProductRecalculateHandler extends AbstractProductHandler
{
    /**
     * @var ProductSpecialsApplicator
     */
    protected $productSpecialsApplicator;

    /**
     * @param LoggerInterface $logger
     * @param ProductSpecialsApplicator $productSpecialsApplicator
     */
    public function __construct(
        LoggerInterface $logger,
        ProductSpecialsApplicator $productSpecialsApplicator
    ) {
        parent::__construct($logger);

        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handleProduct(ProductInterface $product): void
    {
        $this->log(sprintf(
            "Product '%s' recalculate started...",
            (string) $product
        ));

        $this->productSpecialsApplicator->applyToProduct($product);

        $this->log(sprintf(
            "Product '%s' recalculate finished.",
            (string) $product
        ));
    }
}
