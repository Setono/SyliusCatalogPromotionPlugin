<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\ProductInterface;

/**
 * Class AbstractProductHandler
 */
abstract class AbstractProductHandler implements ProductRecalculateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($object): void
    {
        if (!$object instanceof ProductInterface) {
            return;
        }

        $this->handleProduct($object);
    }
}
