<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Message\Command;

use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class AssignEligibleSpecials implements CommandInterface
{
    /** @var int */
    private $productId;

    /**
     * @param ProductInterface|mixed $product
     */
    public function __construct($product)
    {
        $productId = $product;

        if ($product instanceof ProductInterface) {
            $productId = $product->getId();
        }

        Assert::integer($productId);

        $this->productId = $productId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
