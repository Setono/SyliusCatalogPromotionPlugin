<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;

/**
 * Interface SpecialRecalculateHandlerInterface
 */
interface SpecialRecalculateHandlerInterface extends HandlerInterface
{
    /**
     * @param SpecialInterface $special
     */
    public function handle(SpecialInterface $special): void;
}
