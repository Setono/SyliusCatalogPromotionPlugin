<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;

/**
 * Class ProductRecalculateHandler
 */
class ProductRecalculateHandler extends AbstractProductHandler
{
    /**
     * @var ProductSpecialsApplicator
     */
    protected $productSpecialsApplicator;

    /**
     * ProductRecalculateHandler constructor.
     *
     * @param ProductSpecialsApplicator $productSpecialsApplicator
     */
    public function __construct(
        ProductSpecialsApplicator $productSpecialsApplicator
    ) {
        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handleProduct(ProductInterface $product): void
    {
        $this->productSpecialsApplicator->applyToProduct($product);
    }
}
