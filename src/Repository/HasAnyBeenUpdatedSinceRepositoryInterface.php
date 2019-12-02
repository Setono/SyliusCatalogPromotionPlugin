<?php

declare(strict_types=1);

namespace Setono\SyliusCatalogPromotionPlugin\Repository;

use DateTimeInterface;

interface HasAnyBeenUpdatedSinceRepositoryInterface
{
    /**
     * @return bool Returns true if any entity was updated AFTER $dateTime
     */
    public function hasAnyBeenUpdatedSince(DateTimeInterface $dateTime): bool;
}
