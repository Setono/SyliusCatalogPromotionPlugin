<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Handler;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialSubjectInterface;

/**
 * Interface ProductRecalculateHandlerInterface
 */
interface ProductRecalculateHandlerInterface extends HandlerInterface
{
    /**
     * @param SpecialSubjectInterface $product
     */
    public function handle(SpecialSubjectInterface $product): void;
}
