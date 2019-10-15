<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;
use Setono\SyliusBulkSpecialsPlugin\Special\Applicator\ProductSpecialsApplicator;

class ProductRecalculateHandler extends AbstractProductHandler
{
    /** @var ProductSpecialsApplicator */
    protected $productSpecialsApplicator;

    public function __construct(
        LoggerInterface $logger,
        ProductSpecialsApplicator $productSpecialsApplicator
    ) {
        parent::__construct($logger);

        $this->productSpecialsApplicator = $productSpecialsApplicator;
    }

    /**
     * @throws StringsException
     * @throws ORMException
     */
    public function handleProduct(ProductInterface $product): void
    {
        $this->log(sprintf(
            "Product '%s' recalculate started...",
            $product->getCode()
        ));

        $this->productSpecialsApplicator->applyToProduct($product);

        $this->log(sprintf(
            "Product '%s' recalculate finished.",
            $product->getCode()
        ));
    }
}
