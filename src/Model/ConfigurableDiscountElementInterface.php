<?php

declare(strict_types=1);

namespace Setono\SyliusBulkDiscountPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ConfigurableDiscountElementInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType(): ?string;

    public function getConfiguration(): array;

    public function getDiscount(): ?DiscountInterface;
}
