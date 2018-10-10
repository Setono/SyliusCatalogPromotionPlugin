<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;

/**
 * Class ProductRecalculateHandler
 */
class ProductRecalculateHandler implements ProductRecalculateHandlerInterface
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
     * @param SpecialSubjectInterface $product
     */
    public function handle(SpecialSubjectInterface $product): void
    {
        $this->productSpecialsApplicator->applyToProduct($product);
    }
}
