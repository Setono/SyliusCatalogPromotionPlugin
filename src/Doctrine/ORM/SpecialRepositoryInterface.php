<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Doctrine\ORM;

use Setono\SyliusBulkSpecialsPlugin\Model\SpecialInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SpecialRepositoryInterface extends RepositoryInterface
{
    /**
     * Actually, that is not accidentally, just time going on...
     *
     *
     * @return SpecialInterface[]
     */
    public function findAccidentallyDisabled(?\DateTimeInterface $date = null): array;

    /**
     * Actually, that is not accidentally, just time going on...
     *
     *
     * @return SpecialInterface[]
     */
    public function findAccidentallyEnabled(?\DateTimeInterface $date = null): array;

    /**
     * @return SpecialInterface[]
     */
    public function findActive(): array;
}
