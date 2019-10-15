<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

interface SpecialRecalculateHandlerInterface extends HandlerInterface
{
    public function handleSpecial(SpecialInterface $special): void;
}
