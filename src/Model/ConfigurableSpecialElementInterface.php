<?php

declare(strict_types=1);

namespace Setono\SyliusBulkSpecialsPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ConfigurableSpecialElementInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType(): ?string;

    public function getConfiguration(): array;

    public function getSpecial(): ?SpecialInterface;
}
