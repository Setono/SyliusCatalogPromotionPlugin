<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Setono\SyliusBulkSpecialsPlugin\Model\Special;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Interface SpecialRepositoryInterface
 */
interface SpecialRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|Special[]
     */
    public function findActive(): array;
}
