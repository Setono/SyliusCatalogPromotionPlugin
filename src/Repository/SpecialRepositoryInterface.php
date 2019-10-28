<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Repository;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SpecialRepositoryInterface extends RepositoryInterface, HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * This is the method used for processing of specials
     * It should return specials with these properties
     * - Enabled
     * - At least one enabled channel
     * - Sorted by exclusive ascending and thereafter priority
     * - The current time should be within the respective specials time interval
     *
     * @return SpecialInterface[]
     */
    public function findForProcessing(): array;
}
