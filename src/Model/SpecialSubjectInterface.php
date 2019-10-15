<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SpecialSubjectInterface extends ResourceInterface
{
    public function __toString(): string;

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getActiveSpecials(): Collection;

    public function hasExclusiveSpecialsForChannelCode(string $channelCode): bool;

    public function getFirstExclusiveSpecialForChannelCode(string $channelCode): ?SpecialInterface;

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getExclusiveSpecialsForChannelCode(string $channelCode): Collection;

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getActiveSpecialsForChannelCode(string $channelCode): Collection;

    /**
     * @return Collection|SpecialInterface[]
     */
    public function getSpecials(): Collection;

    public function hasSpecial(SpecialInterface $special): bool;

    public function addSpecial(SpecialInterface $special): void;

    public function removeSpecials(): void;

    public function removeSpecial(SpecialInterface $special): void;
}
