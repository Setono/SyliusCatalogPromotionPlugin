<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

abstract class AbstractSpecialHandler extends AbstractHandler implements SpecialRecalculateHandlerInterface
{
    public function handle($object): void
    {
        if (!$object instanceof SpecialInterface) {
            return;
        }

        $this->handleSpecial($object);
    }
}
