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

    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @return SpecialInterface|null
     */
    public function getSpecial(): ?SpecialInterface;
}
