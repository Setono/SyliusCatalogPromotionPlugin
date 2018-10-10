<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Class AbstractSpecialHandler
 */
abstract class AbstractSpecialHandler implements SpecialRecalculateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($object): void
    {
        if (!$object instanceof SpecialInterface) {
            return;
        }

        $this->handleSpecial($object);
    }
}
